with import (fetchTarball "https://github.com/NixOS/nixpkgs/archive/e24b4c09e963677b1beea49d411cd315a024ad3a.tar.gz") {};
myEnv.pkgs.buildEnv {
  name = "e24b4c09e963677b1beea49d411cd315a024ad3a-env";
  paths = [
    (php81.withExtensions (pe: pe.enabled ++ []))
    libmysqlclient
    nginx
    nodejs_20
    npm-9_x
    php81Packages.composer
  ];
}
