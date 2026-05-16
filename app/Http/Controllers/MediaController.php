<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Media;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class MediaController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Event $event)
    {
        $request->validate([
            'files.*' => 'required|file|mimes:jpg,jpeg,png,mp4,mov|max:51200', // max 50MB
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                $fileType = in_array($extension, ['mp4', 'mov']) ? 'video' : 'image';
                $filePath = null;

                // Tentative d'upload vers Cloudinary
                if (config('cloudinary.cloud_url')) {
                    try {
                        $cloudinaryFile = Cloudinary::upload($file->getRealPath(), [
                            'folder' => "events/{$event->id}",
                            'resource_type' => 'auto'
                        ]);
                        $filePath = $cloudinaryFile->getSecurePath();
                    } catch (\Exception $e) {
                        // Fallback local en cas d'échec
                        $filePath = $file->store("events/{$event->id}", 'public');
                    }
                } else {
                    $filePath = $file->store("events/{$event->id}", 'public');
                }

                $event->media()->create([
                    'user_id' => $request->user()->id,
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                    'title' => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Médias ajoutés avec succès !');
    }

        /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $media)
    {
        if (is_string($media->file_path) && preg_match('/^https?:\/\//i', $media->file_path)) {
            try {
                // Extrait l'identifiant public requis par Cloudinary depuis l'URL complète
                // Exemple : extrait "events/1/gef34w59pgcrvmqvceqf"
                $pathSections = explode('/', parse_url($media->file_path, PHP_URL_PATH));
                $filenameWithExtension = end($pathSections);
                $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME);
                
                // Retrouve le dossier de stockage (ex: events/1)
                $targetIndex = array_search('events', $pathSections);
                if ($targetIndex !== false) {
                    $folderPath = implode('/', array_slice($pathSections, $targetIndex, -1));
                    $publicId = $folderPath . '/' . $filename;
                } else {
                    $publicId = $filename;
                }

                // Utilisation de la méthode de destruction de l'API avec le bon ID
                \CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary::destroy($publicId);
            } catch (\Exception $e) {
                // On continue silencieusement pour éviter de bloquer l'interface si l'image est déjà supprimée
            }
        } else {
            // Suppression sur le stockage local traditionnel
            \Illuminate\Support\Facades\Storage::disk('public')->delete($media->file_path);
        }

        $media->delete();

        return redirect()->back();
    }

}
