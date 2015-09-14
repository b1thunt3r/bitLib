<?php
namespace Bit0 {
  class Phar {
    private $FileName;
    private $Folder;
    private $CopyPath;

    public function __construct($file, $dir, array $copyTo = []) {
      $this->FileName = $file;
      $this->Folder = realpath($dir);
      $this->CopyPath = $copyTo;
    }

    public function Create() {
      $file = new \SplFileInfo($this->FileName);

      if ($file->isFile())
          \Phar::unlinkArchive($file->getRealPath());

      $phar = new \Phar(
          $file->getFilename(),
          \FilesystemIterator::CURRENT_AS_FILEINFO |
            \FilesystemIterator::KEY_AS_FILENAME,
          $file->getBasename($file->getExtension()).date('YmdHis_u').'.phar'
      );

      $phar->buildFromDirectory($this->Folder);
      $phar->setStub(file_get_contents($this->Folder.'/stub.php'));
    }

    public function Extract() {
      try {
        $phar = new \Phar($this->FileName);
        $phar->extractTo($this->Folder);
      } catch(Exception $e) {
        // handle errors
      }
    }
  }
}
