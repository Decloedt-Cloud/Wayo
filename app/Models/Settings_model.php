<?php

namespace App\Models;

use CodeIgniter\Model;

class Settings_model extends Model {
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField    = 'deleted_at';
    protected $dateFormat       = 'datetime';

    protected $DBGroup = 'default';

    protected $request;
    protected $session;

    public function __construct()
    {
        parent::__construct();
        $this->request = \Config\Services::request();
        $this->session = \Config\Services::session();
    }

  private function uploadedFile(string $field)
  {
    return $this->request->getFile($field);
  }

  private function isUploadOk(string $field): bool
  {
    $file = $this->uploadedFile($field);
    return $file && $file->getError() === UPLOAD_ERR_OK;
  }

  public function update_system_settings()
  {
    $data['system_name'] = htmlspecialchars($this->request->getPost('system_name'));
    $data['system_email'] = htmlspecialchars($this->request->getPost('system_email'));
    $data['system_title'] = htmlspecialchars($this->request->getPost('system_title'));
    $data['phone'] = htmlspecialchars($this->request->getPost('phone'));
    $data['purchase_code'] = htmlspecialchars($this->request->getPost('purchase_code'));
    $data['address'] = htmlspecialchars($this->request->getPost('address'));
    // $data['fax'] = htmlspecialchars($this->request->getPost('fax'));
    $data['footer_text'] = htmlspecialchars($this->request->getPost('footer_text'));
    $data['footer_link'] = htmlspecialchars($this->request->getPost('footer_link'));
    $data['timezone'] = htmlspecialchars($this->request->getPost('timezone'));
    $data['youtube_api_key'] = htmlspecialchars($this->request->getPost('youtube_api_key'));
    $data['vimeo_api_key'] = htmlspecialchars($this->request->getPost('vimeo_api_key'));
    \db()->table('settings')->where('id', 1)->update($data);
    $response = array(
      'status' => true,
      'notification' => get_phrase('system_settings_updated_successfully')
    );
    return json_encode($response);
  }

  public function last_updated_attendance_data()
  {
    $data['date_of_last_updated_attendance'] = strtotime(date('d-m-Y H:i:s'));
    \db()->table('settings')->where('id', 1)->update($data);
  }


  public function update_system_logo() {
    
    // Définition du type MIME pour les fichiers SVG
    $svg_type = 'image/svg+xml';

    // Handle Dark Logo
    if ($this->isUploadOk('dark_logo')) {// Vérifie si un fichier a été uploadé
        $darkLogo = $this->uploadedFile('dark_logo');
        $file_type = mime_content_type($darkLogo->getTempName()); //Détecte le type MIME du fichier
        $destination = 'uploads/system/logo/logo-dark';// Définition du chemin de stockage

        // Si le fichier est un SVG
        if ($file_type === $svg_type) {
           // Supprime l'ancienne version PNG s'il existe
            if (file_exists($destination . '.png')) unlink($destination . '.png');
            // Sauvegarde le nouveau fichier en tant que SVG
            $darkLogo->move('uploads/system/logo', 'logo-dark.svg', true);

        } else {// Si le fichier n'est pas un SVG (donc PNG par défaut)
          // Supprime l'ancienne version SVG s'il existe
            if (file_exists($destination . '.svg')) unlink($destination . '.svg');
            // Sauvegarde le nouveau fichier en tant que PNG
            $darkLogo->move('uploads/system/logo', 'logo-dark.png', true);
        }
    }

    // Handle Light Logo
    if ($this->isUploadOk('light_logo')) {
        $lightLogo = $this->uploadedFile('light_logo');
        $file_type = mime_content_type($lightLogo->getTempName());
        $destination = 'uploads/system/logo/logo-light';

        if ($file_type === $svg_type) {
            if (file_exists($destination . '.png')) unlink($destination . '.png');
            $lightLogo->move('uploads/system/logo', 'logo-light.svg', true);
        } else {
            if (file_exists($destination . '.svg')) unlink($destination . '.svg');
            $lightLogo->move('uploads/system/logo', 'logo-light.png', true);
        }
    }

    // Handle Small Logo
    if ($this->isUploadOk('small_logo')) {
        $smallLogo = $this->uploadedFile('small_logo');
        $file_type = mime_content_type($smallLogo->getTempName());
        $destination = 'uploads/system/logo/logo-light-sm';

        if ($file_type === $svg_type) {
            if (file_exists($destination . '.png')) unlink($destination . '.png');
            $smallLogo->move('uploads/system/logo', 'logo-light-sm.svg', true);
        } else {
            if (file_exists($destination . '.svg')) unlink($destination . '.svg');
            $smallLogo->move('uploads/system/logo', 'logo-light-sm.png', true);
        }
    }

    // Handle Favicon
    if ($this->isUploadOk('favicon')) {
        $favicon = $this->uploadedFile('favicon');
        $file_type = mime_content_type($favicon->getTempName());
        $destination = 'uploads/system/logo/favicon';

        if ($file_type === $svg_type) {
            if (file_exists($destination . '.png')) unlink($destination . '.png');
            $favicon->move('uploads/system/logo', 'favicon.svg', true);
        } else {
            if (file_exists($destination . '.svg')) unlink($destination . '.svg');
            $favicon->move('uploads/system/logo', 'favicon.png', true);
        }
    }
    $response = array(
        'status' => true,// Indique que l'opération a réussi
        'notification' => get_phrase('logo_updated_successfully') // Message de confirmation
    );
    return json_encode($response);// Retourne la réponse au format JSON
}

  // SCHOOL SETTINGS
  public function get_current_school_data($school_id = null)
  {
    if ($school_id === null) {
        $school_id = session()->get('school_id');
    }
    return \db()->table('schools')->where('id', $school_id)->get()->getRowArray();
  }

  /**
   * Compresse et redimensionne une image uploadée
   * Préserve la qualité tout en optimisant la taille du fichier
   * Utilise plusieurs stratégies de fallback pour maximiser la compatibilité
   * 
   * @param string $source_path Chemin du fichier temporaire uploadé
   * @param string $destination_path Chemin de destination (sans extension)
   * @param int $max_width Largeur maximale recommandée
   * @param int $max_height Hauteur maximale recommandée
   * @param int $quality Qualité de compression (1-100), défaut 90
   * @return bool|string Retourne le chemin final ou false en cas d'erreur
   */
  private function compress_and_save_image($source_path, $destination_path, $max_width = 512, $max_height = 512, $quality = 90)
  {
      // Vérifier que le fichier existe
      if (!file_exists($source_path)) {
          log_message('error', 'Image compression: Source file not found - ' . $source_path);
          return false;
      }

      // Vérifier que GD est disponible
      if (!extension_loaded('gd')) {
          log_message('error', 'Image compression: GD extension not available');
          return false;
      }

      // Obtenir les informations de l'image
      $image_info = @getimagesize($source_path);
      if ($image_info === false) {
          log_message('error', 'Image compression: Unable to get image info - ' . $source_path);
          return false;
      }

      $original_width = $image_info[0];
      $original_height = $image_info[1];
      $mime_type = $image_info['mime'];

      // Vérifier les dimensions minimales
      if ($original_width < 1 || $original_height < 1) {
          log_message('error', 'Image compression: Invalid image dimensions');
          return false;
      }

      // Créer l'image source - Stratégie multi-fallback
      $source_image = $this->create_image_from_file($source_path, $mime_type);
      
      if ($source_image === false) {
          log_message('error', 'Image compression: Failed to create image resource after all attempts');
          return false;
      }

      // Calculer les nouvelles dimensions en préservant le ratio
      list($new_width, $new_height) = $this->calculate_dimensions(
          $original_width, $original_height, $max_width, $max_height
      );

      // Créer et traiter l'image de destination
      $destination_image = $this->create_destination_image($source_image, $original_width, $original_height, $new_width, $new_height, $mime_type);

      if ($destination_image === false) {
          imagedestroy($source_image);
          return false;
      }

      // Sauvegarder avec fallback de qualité
      $final_path = $destination_path . '.jpg';
      $save_result = $this->save_optimized_jpeg($destination_image, $final_path, $quality);

      // Libérer la mémoire
      imagedestroy($source_image);
      imagedestroy($destination_image);

      if (!$save_result) {
          // Nettoyer le fichier partiellement créé s'il existe
          if (file_exists($final_path)) {
              @unlink($final_path);
          }
          log_message('error', 'Image compression: Failed to save image - ' . $final_path);
          return false;
      }

      // Log du résultat
      $this->log_compression_result($source_path, $final_path, $original_width, $original_height, $new_width, $new_height);

      return $final_path;
  }

  /**
   * Crée une ressource image depuis un fichier avec plusieurs stratégies
   */
  private function create_image_from_file($source_path, $mime_type)
  {
      $source_image = false;

      // Stratégie 1: Selon le type MIME
      switch ($mime_type) {
          case 'image/jpeg':
          case 'image/jpg':
              $source_image = @imagecreatefromjpeg($source_path);
              break;
          case 'image/png':
              $source_image = @imagecreatefrompng($source_path);
              break;
          case 'image/gif':
              $source_image = @imagecreatefromgif($source_path);
              break;
          case 'image/webp':
              if (function_exists('imagecreatefromwebp')) {
                  $source_image = @imagecreatefromwebp($source_path);
              }
              break;
          case 'image/bmp':
          case 'image/x-ms-bmp':
              if (function_exists('imagecreatefrombmp')) {
                  $source_image = @imagecreatefrombmp($source_path);
              }
              break;
      }

      // Stratégie 2: Fallback avec imagecreatefromstring
      if ($source_image === false) {
          $image_data = @file_get_contents($source_path);
          if ($image_data !== false) {
              $source_image = @imagecreatefromstring($image_data);
          }
      }

      // Stratégie 3: Essayer tous les formats connus
      if ($source_image === false) {
          $functions = ['imagecreatefromjpeg', 'imagecreatefrompng', 'imagecreatefromgif'];
          if (function_exists('imagecreatefromwebp')) $functions[] = 'imagecreatefromwebp';
          if (function_exists('imagecreatefrombmp')) $functions[] = 'imagecreatefrombmp';
          
          foreach ($functions as $func) {
              $source_image = @$func($source_path);
              if ($source_image !== false) {
                  log_message('info', "Image loaded using fallback: $func");
                  break;
              }
          }
      }

      return $source_image;
  }

  /**
   * Calcule les nouvelles dimensions en préservant le ratio
   */
  private function calculate_dimensions($original_width, $original_height, $max_width, $max_height)
  {
      $new_width = $original_width;
      $new_height = $original_height;

      if ($original_width > $max_width || $original_height > $max_height) {
          $ratio_width = $max_width / $original_width;
          $ratio_height = $max_height / $original_height;
          $ratio = min($ratio_width, $ratio_height);
          
          $new_width = max(1, (int) round($original_width * $ratio));
          $new_height = max(1, (int) round($original_height * $ratio));
      }

      return [$new_width, $new_height];
  }

  /**
   * Crée et configure l'image de destination avec resampling haute qualité
   */
  private function create_destination_image($source_image, $original_width, $original_height, $new_width, $new_height, $mime_type)
  {
      $destination_image = @imagecreatetruecolor($new_width, $new_height);

      if ($destination_image === false) {
          log_message('error', 'Image compression: Failed to create destination image');
          return false;
      }

      // Configurer selon le type source
      if ($mime_type === 'image/png') {
          // Préserver la transparence pour PNG (fond blanc pour JPEG final)
          $white = imagecolorallocate($destination_image, 255, 255, 255);
          imagefilledrectangle($destination_image, 0, 0, $new_width, $new_height, $white);
      } else {
          // Fond blanc pour tous les autres formats
          $white = imagecolorallocate($destination_image, 255, 255, 255);
          imagefilledrectangle($destination_image, 0, 0, $new_width, $new_height, $white);
      }

      // Activer l'interpolation de haute qualité (JPEG progressif)
      imageinterlace($destination_image, true);

      // Redimensionner avec resampling de haute qualité
      $resample_result = imagecopyresampled(
          $destination_image,
          $source_image,
          0, 0, 0, 0,
          $new_width, $new_height,
          $original_width, $original_height
      );

      if (!$resample_result) {
          imagedestroy($destination_image);
          log_message('error', 'Image compression: Resampling failed');
          return false;
      }

      return $destination_image;
  }

  /**
   * Sauvegarde en JPEG optimisé avec fallback de qualité si nécessaire
   */
  private function save_optimized_jpeg($image, $path, $quality)
  {
      // Essayer avec la qualité demandée
      $result = @imagejpeg($image, $path, $quality);
      
      if ($result && file_exists($path)) {
          $file_size = filesize($path);
          
          // Si le fichier est trop gros (> 500KB), réduire la qualité progressivement
          $max_size = 500 * 1024; // 500 KB
          $min_quality = 70;
          
          while ($file_size > $max_size && $quality > $min_quality) {
              $quality -= 5;
              $result = @imagejpeg($image, $path, $quality);
              if ($result && file_exists($path)) {
                  $file_size = filesize($path);
                  log_message('info', "Image quality reduced to $quality% - Size: " . $this->format_bytes($file_size));
              } else {
                  break;
              }
          }
      }

      return $result && file_exists($path);
  }

  /**
   * Log les résultats de la compression
   */
  private function log_compression_result($source_path, $final_path, $original_width, $original_height, $new_width, $new_height)
  {
      $original_size = @filesize($source_path) ?: 0;
      $new_size = @filesize($final_path) ?: 0;
      $reduction = $original_size > 0 ? round((1 - ($new_size / $original_size)) * 100, 1) : 0;
      
      log_message('info', sprintf(
          'Image compressed: %dx%d -> %dx%d, %s -> %s (%.1f%% reduction)',
          $original_width, $original_height,
          $new_width, $new_height,
          $this->format_bytes($original_size),
          $this->format_bytes($new_size),
          max(0, $reduction)
      ));
  }

  /**
   * Formate une taille en bytes de manière lisible
   * @param int $bytes
   * @return string
   */
  private function format_bytes($bytes)
  {
      $units = ['B', 'KB', 'MB', 'GB'];
      $bytes = max($bytes, 0);
      $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
      $pow = min($pow, count($units) - 1);
      $bytes /= pow(1024, $pow);
      return round($bytes, 2) . ' ' . $units[$pow];
  }

  public function get_current_settings_school_data()
  {
    $school_id = session()->get('school_id');
    return \db()->table('settings_school')->where('school_id', $school_id)->get()->getRowArray();
  }
  public function get_settings_school_data($school_id)
  {
    return \db()->table('settings_school')->where('school_id', $school_id)->get()->getRowArray();
  }
  public function update_current_school_settings()
  {
    $schoolId = school_id();
    
    $data['name'] = htmlspecialchars($this->request->getPost('school_name'));
    $data['phone'] = htmlspecialchars($this->request->getPost('phone'));
    $data['Rue'] = htmlspecialchars($this->request->getPost('communityStreet'));
    $data['Numero'] = htmlspecialchars($this->request->getPost('communityNumber'));
    $data['Ville'] = htmlspecialchars($this->request->getPost('communityCity'));
    $data['Codepostal'] = htmlspecialchars($this->request->getPost('communityPostalCode'));
    $data['description'] = htmlspecialchars($this->request->getPost('description'));
    $data['access'] = htmlspecialchars($this->request->getPost('access'));
    // $data['category'] was being overwritten. Assuming 'category' input is for school category
    $data['category'] = htmlspecialchars_decode($this->request->getPost('category'));
    
    // Validate Tax Residence and prepare country code
    $tax_residence = htmlspecialchars_decode($this->request->getPost('tax_residence'));
  
    // Validation Tax Residence
    if (!in_array($tax_residence, ['MA', 'UAE','AE'])) {
        log_message('error', 'Invalid Tax Residence value: ' . $tax_residence);
        return json_encode(['status' => false, 'notification' => 'Invalid Tax Residence value']);
    }
    
    // Set country code based on tax residence (or use tax residence directly if it IS the code)
    $country_code = $tax_residence;
    $data['country'] = $country_code;

    \db()->table('schools')->where('id', $schoolId)->update($data);

    // ----------------- Upload logo (compressé) -----------------
    if($this->isUploadOk('school_image')) {
        $schoolImage = $this->uploadedFile('school_image');
        $logo_path = 'uploads/schools/';
        if(!is_dir($logo_path)){
            mkdir($logo_path, 0777, true);
        }
        
        // Compresser et sauvegarder le logo (512x512 recommandé, qualité 90%)
        $logo_result = $this->compress_and_save_image(
            $schoolImage->getTempName(),
            $logo_path . $schoolId,
            512,   // max width
            512,   // max height
            90     // qualité JPEG
        );
        
        if ($logo_result === false) {
            log_message('error', 'Failed to compress school logo for school_id: ' . $schoolId);
            return json_encode([
                'status' => false, 
                'error_type' => 'logo',
                'error_message' => get_phrase('image_processing_failed_please_try_another_image')
            ]);
        }
    }

    // ----------------- Upload cover (compressé) -----------------
    if($this->isUploadOk('school_cover')) {
        $schoolCover = $this->uploadedFile('school_cover');
        $cover_path = 'uploads/communityCover/';
        if(!is_dir($cover_path)){
            mkdir($cover_path, 0777, true);
        }
        
        // Compresser et sauvegarder la cover (1920x600 recommandé, qualité 90%)
        $cover_result = $this->compress_and_save_image(
            $schoolCover->getTempName(),
            $cover_path . $schoolId,
            1920,  // max width
            600,   // max height
            90     // qualité JPEG
        );
        
        if ($cover_result === false) {
            log_message('error', 'Failed to compress school cover for school_id: ' . $schoolId);
            return json_encode([
                'status' => false, 
                'error_type' => 'cover',
                'error_message' => get_phrase('cover_image_processing_failed_please_try_another_image')
            ]);
        }
    }
    
    // ----------------- Settings school -----------------
 
    // Tax_residence est maintenant stocké dans schools.country (source unique de vérité)
    $data_settings_school['type'] = htmlspecialchars_decode($this->request->getPost('i_am'));
    $data_settings_school['num_vat'] = htmlspecialchars_decode($this->request->getPost('num_vat'));


    
    // Gestion de la suppression du document
    if ($this->request->getPost('delete_tax_document') == '1') {
        // Récupérer le nom du fichier actuel
        $current_settings = \db()->table('settings_school')->where('school_id', school_id())->get()->getRowArray();
        if (!empty($current_settings['file'])) {
            $file_path = 'uploads/community_tax/' . $current_settings['file'];
            // Supprimer le fichier s'il existe
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $data_settings_school['file'] = NULL;
        }
    }

    // Validate the uploaded file
    if ($this->isUploadOk('tax_document')) {
        $taxDocument = $this->uploadedFile('tax_document');
        $allowed_extensions = ['pdf', 'jpg', 'png', 'jpeg'];
        $file_ext = strtolower(pathinfo($taxDocument->getClientName(), PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_extensions)) {
            log_message('error', 'Invalid file extension: ' . $file_ext);
            return json_encode(['status' => false, 'notification' => 'Invalid file type. Only PDF, JPG, and PNG are allowed.']);
        }

        // Vérifier la taille du fichier (4 Mo max)
        $max_file_size = 4 * 1024 * 1024; // 4 Mo en bytes
        if ($taxDocument->getSize() > $max_file_size) {
            log_message('error', 'File too large: ' . $taxDocument->getSize());
            return json_encode(['status' => false, 'notification' => 'File is too large. Maximum size is 4 MB.']);
        }

        // Supprimer l'ancien fichier s'il existe
        $current_settings = \db()->table('settings_school')->where('school_id', school_id())->get()->getRowArray();
        if (!empty($current_settings['file'])) {
            $old_file_path = 'uploads/community_tax/' . $current_settings['file'];
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }
        }

        $file_name = md5(rand(10000000, 20000000)) . '.' . $file_ext;
        $upload_path = 'uploads/community_tax/';
        
        // Créer le répertoire s'il n'existe pas
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        if (!$taxDocument->move($upload_path, $file_name, true)) {
            log_message('error', 'Failed to move uploaded file to ' . $upload_path . $file_name);
            return json_encode(['status' => false, 'notification' => 'Failed to upload the file.']);
        }

        $data_settings_school['file'] = $file_name;
    } 
    // else {
    //     log_message('error', 'File upload error or no file uploaded.');
    //     return json_encode(['status' => false, 'notification' => 'No file uploaded or upload error.']);
    // }

    \db()->table('settings_school')->where('school_id', $schoolId)->update($data_settings_school);

    $response = array(
      'status' => true,
      'notification' => get_phrase('school_settings_updated_successfully')
    );
    return json_encode($response);
  }

  // Delete tax document
  public function delete_tax_document()
  {
    $schoolId = school_id();
    
    // Récupérer les paramètres actuels
    $current_settings = \db()->table('settings_school')->where('school_id', school_id())->get()->getRowArray();
    
    if (empty($current_settings['file'])) {
      return json_encode([
        'status' => false,
        'notification' => get_phrase('No document found to delete')
      ]);
    }

    $file_path = 'uploads/community_tax/' . $current_settings['file'];
    
    // Supprimer le fichier s'il existe
    if (file_exists($file_path)) {
      if (unlink($file_path)) {
        // Mettre à jour la base de données
        \db()->table('settings_school')->where('school_id', $schoolId)->update(array('file' => NULL));
        
        return json_encode([
          'status' => true,
          'notification' => get_phrase('Document deleted successfully')
        ]);
      } else {
        return json_encode([
          'status' => false,
          'notification' => get_phrase('Failed to delete file')
        ]);
      }
    } else {
      // Le fichier n'existe plus, mais on supprime quand même la référence en base
      \db()->table('settings_school')->where('school_id', $schoolId)->update(array('file' => NULL));
      
      return json_encode([
        'status' => true,
        'notification' => get_phrase('Document reference removed successfully')
      ]);
    }
  }

  // PAYMENT SETTINGS
  public function update_system_currency_settings()
  {
    $school_id = (int) school_id();
    $currency = strtoupper(trim((string) $this->request->getPost('system_currency')));
    $currency_position = trim((string) $this->request->getPost('currency_position'));
    $allowed_positions = ['left', 'right', 'left-space', 'right-space'];

    if ($currency === '' || !in_array($currency_position, $allowed_positions, true)) {
      return json_encode([
        'status' => false,
        'notification' => get_phrase('please_fill_all_the_fields'),
      ]);
    }

    $data = [
      'system_currency' => htmlspecialchars($currency, ENT_QUOTES, 'UTF-8'),
      'currency_position' => htmlspecialchars($currency_position, ENT_QUOTES, 'UTF-8'),
    ];

    $settings_table = \db()->table('settings_school');
    $existing = $settings_table->where('school_id', $school_id)->countAllResults();
    if ($existing > 0) {
      \db()->table('settings_school')->where('school_id', $school_id)->update($data);
    } else {
      $data['school_id'] = $school_id;
      \db()->table('settings_school')->insert($data);
    }

    return json_encode([
      'status' => true,
      'notification' => get_phrase('school_settings_updated_successfully')
    ]);
  }
    public function toggle_community_etat()
  {
    $is_public = intval($this->request->getPost('etat'));
    // Convert: toggle sends 1=public, 0=private
    // access column: 0=public, 1=private
    $access = ($is_public == 1) ? 0 : 1;

    $user_id = session()->get('user_id');
    if (strtolower(\db()->table('users')->where('school_id', school_id())->get()->getRowArray()['role']) == 'admin') {
        $target_school_id = school_id();
    } else {
        $target_school_id = 1;
    }

    $data = array('access' => $access);

    // If switching to private (access = 1), also reset price to 0
    if ($access == 1) {
        $data['price'] = 0;
    }

    \db()->table('schools')->where('id', $target_school_id)->update($data);

    $response = array(
      'status' => true,
      'notification' => get_phrase('community_state_updated_successfully')
    );
    return json_encode($response);
  }

    public function update_system_price()
  {
    $target_school_id = (int) school_id();

    $school = \db()->table('schools')->where('id', $target_school_id)->get()->getRowArray();

    // Block price update if community is private
    if (isset($school['access']) && $school['access'] == 1) {
        $response = [
          'status' => false,
          'notification' => get_phrase('you_cannot_monetize_a_private_community')
        ];
        return json_encode($response);
    }

    $price_raw = trim((string) $this->request->getPost('price_community'));
    if ($price_raw === '' || !is_numeric($price_raw)) {
      return json_encode([
        'status' => false,
        'notification' => get_phrase('please_provide_valid_price')
      ]);
    }

    $price = (float) $price_raw;
    if ($price < 0) {
      return json_encode([
        'status' => false,
        'notification' => get_phrase('please_provide_valid_price')
      ]);
    }

    $data['price'] = number_format($price, 2, '.', '');

    \db()->table('schools')->where('id', $target_school_id)->update($data);

    $response = array(
      'status' => true,
      'notification' => get_phrase('system_settings_updated_successfully')
    );
    return json_encode($response);
  }
  public function update_system_vat()
  {
    $school_id = (int) school_id();
    $vat_applicable = (string) $this->request->getPost('vat_applicable');
    $vat_enabled = $vat_applicable === '1' ? 1 : 0;

    if ($vat_applicable !== '0' && $vat_applicable !== '1') {
      return json_encode([
        'status' => false,
        'notification' => get_phrase('please_fill_all_the_fields')
      ]);
    }

    $vat_rate = '--';
    if ($vat_enabled === 1) {
      $vat_rate_raw = trim((string) $this->request->getPost('vat_rate'));
      $vat_rate_raw = str_replace('%', '', $vat_rate_raw);
      if ($vat_rate_raw === '' || !is_numeric($vat_rate_raw)) {
        return json_encode([
          'status' => false,
          'notification' => get_phrase('please_provide_valid_vat_rate')
        ]);
      }

      $vat_rate_number = (float) $vat_rate_raw;
      if ($vat_rate_number < 0 || $vat_rate_number > 100) {
        return json_encode([
          'status' => false,
          'notification' => get_phrase('please_provide_valid_vat_rate')
        ]);
      }

      $vat_rate = rtrim(rtrim(number_format($vat_rate_number, 2, '.', ''), '0'), '.') . '%';
    }

    $data = [
      'vat' => $vat_enabled,
      'vat_enabled' => $vat_enabled,
      'vat_rate' => htmlspecialchars($vat_rate, ENT_QUOTES, 'UTF-8'),
    ];

    $settings_table = \db()->table('settings_school');
    $existing = $settings_table->where('school_id', $school_id)->countAllResults();
    if ($existing > 0) {
      \db()->table('settings_school')->where('school_id', $school_id)->update($data);
    } else {
      $data['school_id'] = $school_id;
      \db()->table('settings_school')->insert($data);
    }

    return json_encode([
      'status' => true,
      'notification' => get_phrase('system_settings_updated_successfully')
    ]);
  }
  public function update_paypal_settings()
  {
    $paypal_info = array();

    $paypal['paypal_active'] = htmlspecialchars($this->request->getPost('paypal_active'));
    $paypal['paypal_mode'] = htmlspecialchars($this->request->getPost('paypal_mode'));
    $paypal['paypal_client_id_sandbox'] = htmlspecialchars($this->request->getPost('paypal_client_id_sandbox'));
    $paypal['paypal_client_id_production'] = htmlspecialchars($this->request->getPost('paypal_client_id_production'));
    $paypal['paypal_currency'] = htmlspecialchars($this->request->getPost('paypal_currency'));

    array_push($paypal_info, $paypal);

    $data['value'] = json_encode($paypal_info);
    \db()->table('payment_settings')->where('key', 'paypal_settings')->where('school_id', session()->get('school_id'))->update($data);

    $response = array(
      'status' => true,
      'notification' => get_phrase('stripe_settings_updated_successfully')
    );
    return json_encode($response);
  }

  public function update_stripe_settings()
  {
    $stripe_info = array();
    $stripe['stripe_active'] = htmlspecialchars($this->request->getPost('stripe_active'));
    $stripe['stripe_mode'] = htmlspecialchars($this->request->getPost('stripe_mode'));
    $stripe['stripe_test_secret_key'] = htmlspecialchars($this->request->getPost('stripe_test_secret_key'));
    $stripe['stripe_test_public_key'] = htmlspecialchars($this->request->getPost('stripe_test_public_key'));
    $stripe['stripe_live_secret_key'] = htmlspecialchars($this->request->getPost('stripe_live_secret_key'));
    $stripe['stripe_live_public_key'] = htmlspecialchars($this->request->getPost('stripe_live_public_key'));
    $stripe['stripe_currency'] = htmlspecialchars($this->request->getPost('stripe_currency'));

    array_push($stripe_info, $stripe);

    $data['value'] = json_encode($stripe_info);
    \db()->table('payment_settings')->where('key', 'stripe_settings')->where('school_id', session()->get('school_id'))->update($data);

    $response = array(
      'status' => true,
      'notification' => get_phrase('paypal_settings_updated_successfully')
    );
    return json_encode($response);
  }

  // UPDATE SMTP CREDENTIALS
  public function update_smtp_settings()
  {
    if ($this->request->getPost('mail_sender') == 'php_mailer') {
      if (empty($this->request->getPost('smtp_secure')) || empty($this->request->getPost('smtp_set_from')) || empty($this->request->getPost('smtp_show_error'))) {
        $response = array(
          'status' => false,
          'notification' => get_phrase('please_fill_all_the_fields')
        );
        return json_encode($response);
      }
    }

    $data['mail_sender'] = htmlspecialchars($this->request->getPost('mail_sender'));
    $data['smtp_protocol'] = htmlspecialchars($this->request->getPost('smtp_protocol'));
    $data['smtp_host'] = htmlspecialchars($this->request->getPost('smtp_host'));
    $data['smtp_crypto'] = htmlspecialchars($this->request->getPost('smtp_crypto'));
    $data['smtp_username'] = htmlspecialchars($this->request->getPost('smtp_username'));
    $data['smtp_password'] = htmlspecialchars($this->request->getPost('smtp_password'));
    $data['smtp_port'] = htmlspecialchars($this->request->getPost('smtp_port'));

    $data['smtp_secure'] = strtolower($this->request->getPost('smtp_secure'));
    $data['smtp_set_from'] = htmlspecialchars($this->request->getPost('smtp_set_from'));
    $data['smtp_show_error'] = htmlspecialchars($this->request->getPost('smtp_show_error'));

    if (\db()->table('smtp_settings')->countAllResults() > 0) {
      \db()->table('smtp_settings')->where('id', 1)->update($data);
    } else {
      \db()->table('smtp_settings')->insert($data);
    }

    $response = array(
      'status' => true,
      'type' => 'success',
      'notification' => get_phrase('smtp_settings_updated_successfully')
  );

  // Ajoutez ces lignes avant de retourner la réponse
  return json_encode($response);
  }

  // This function is responsible for retreving all the files and folder
  public function get_list_of_directories_and_files($dir = APPPATH, &$results = array())
  {
    $files = scandir($dir);
    foreach ($files as $key => $value) {
      $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
      if (!is_dir($path)) {
        $results[] = $path;
      } else if ($value != "." && $value != "..") {
        $this->get_list_of_directories_and_files($path, $results);
        $results[] = $path;
      }
    }
    return $results;
  }

  // This function is responsible for retreving all the language file from language folder
  function get_list_of_language_files($dir = APPPATH . '/language', &$results = array())
  {
    $files = scandir($dir);
    foreach ($files as $key => $value) {
      $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
      if (!is_dir($path)) {
        $results[] = $path;
      } else if ($value != "." && $value != "..") {
        $this->get_list_of_directories_and_files($path, $results);
        $results[] = $path;
      }
    }
    return $results;
  }

  // LANGUAGE SETTINGS
  public function get_all_languages()
  {
    $language_files = array();
    $all_files = $this->get_list_of_language_files();
    foreach ($all_files as $file) {
      $info = pathinfo($file);
      if (isset($info['extension']) && strtolower($info['extension']) == 'json') {
        $file_name = explode('.json', $info['basename']);
        array_push($language_files, $file_name[0]);
      }
    }
    return $language_files;
  }
  // public function get_all_languages() {
  //   $language_files = array();
  //   \db()->distinct('name');
  //   \db()->select('name');
  //   return \db()->get('language')->getResultArray();
  // }

  public function create_language()
  {
    saveDefaultJSONFile(trimmer($this->request->getPost('language')));
    $response = array(
      'status' => true,
      'notification' => get_phrase('language_added_successfully')
    );
    return json_encode($response);
  }

  public function update_language($param1 = "")
  {
    if (file_exists('app/Language/' . $param1 . '.json')) {
      unlink('app/Language/' . $param1 . '.json');
    }
    saveDefaultJSONFile(trimmer($this->request->getPost('language')));
    $response = array(
      'status' => true,
      'notification' => get_phrase('language_added_successfully')
    );
    return json_encode($response);
  }

  public function delete_language($param1 = "")
  {
    if (file_exists('app/Language/' . $param1 . '.json')) {
      unlink('app/Language/' . $param1 . '.json');
    }
    $response = array(
      'status' => true,
      'notification' => get_phrase('language_deleted_successfully')
    );
    return json_encode($response);
  }

 // Settings_model.php
  public function update_system_language($user_id = "", $selected_language = "") {
    // Map language names to codes for URL prefixes
    $lang_codes = array(
        'french' => 'fr',
        'english' => 'en',
        'arabic' => 'ar',
        'spanish' => 'es',
        'dutch' => 'nl'
    );
    
    if (!empty($user_id)) {
        \db()->table('users')->where('id', $user_id)->update(['language' => $selected_language]);
    } else {
        \db()->table('settings')->where('id', 1)->update(['language' => $selected_language]);
    }
    
    // Update session with the new language and lang_code
    session()->set('language', $selected_language);
    
    // Set the language code for URL prefixes
    $lang_lower = strtolower($selected_language);
    $code = isset($lang_codes[$lang_lower]) ? $lang_codes[$lang_lower] : 'en';
    session()->set('lang_code', $code);
  }
  function get_currencies()
  {
    return \db()->table('currencies')->where('payumoney_supported', 1)->get()->getResultArray();
  }

  function get_paypal_supported_currencies()
  {
    return \db()->table('currencies')->where('paypal_supported', 1)->get()->getResultArray();
  }

  function get_stripe_supported_currencies()
  {
    return \db()->table('currencies')->where('stripe_supported', 1)->get()->getResultArray();
  }

  // ABOUT APPLICATION INFORMATION
  function get_application_details()
  {
    $purchase_code = get_settings('purchase_code');
    $returnable_array = array(
      'purchase_code_status' => get_phrase('not_found'),
      'support_expiry_date' => get_phrase('not_found'),
      'customer_name' => get_phrase('not_found')
    );

    $personal_token = "gC0J1ZpY53kRpynNe4g2rWT5s4MW56Zg";
    $url = "https://api.envato.com/v3/market/author/sale?code=" . $purchase_code;
    $curl = curl_init($url);

    //setting the header for the rest of the api
    $bearer = 'bearer ' . $personal_token;
    $header = array();
    $header[] = 'Content-length: 0';
    $header[] = 'Content-type: application/json; charset=utf-8';
    $header[] = 'Authorization: ' . $bearer;

    $verify_url = 'https://api.envato.com/v1/market/private/user/verify-purchase:' . $purchase_code . '.json';
    $ch_verify = curl_init($verify_url . '?code=' . $purchase_code);

    curl_setopt($ch_verify, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch_verify, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch_verify, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch_verify, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch_verify, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

    $cinit_verify_data = curl_exec($ch_verify);
    curl_close($ch_verify);

    $response = json_decode($cinit_verify_data, true);
    $verify_purchase = (is_array($response) && isset($response['verify-purchase']) && is_array($response['verify-purchase']))
      ? $response['verify-purchase']
      : [];

    if (!empty($verify_purchase)) {

      //print_r($response);
      $item_name = $verify_purchase['item_name'] ?? '';
      $purchase_time = $verify_purchase['created_at'] ?? '';
      $customer = $verify_purchase['buyer'] ?? '';
      $licence_type = $verify_purchase['licence'] ?? '';
      $support_until = $verify_purchase['supported_until'] ?? '';
      $customer = $verify_purchase['buyer'] ?? '';

      $purchase_date = $purchase_time !== '' ? date("d M, Y", strtotime($purchase_time)) : '';

      $todays_timestamp = strtotime(date("d M, Y"));
      $support_expiry_timestamp = $support_until !== '' ? strtotime($support_until) : 0;

      $support_expiry_date = $support_expiry_timestamp ? date("d M, Y", $support_expiry_timestamp) : get_phrase('not_found');

      if ($todays_timestamp > $support_expiry_timestamp)
        $support_status = get_phrase('expired');
      else
        $support_status = get_phrase('valid');

      $returnable_array = array(
        'purchase_code_status' => $support_status,
        'support_expiry_date' => $support_expiry_date,
        'customer_name' => $customer
      );
    } else {
      $returnable_array = array(
        'purchase_code_status' => 'invalid',
        'support_expiry_date' => 'invalid',
        'customer_name' => 'invalid'
      );
    }

    return $returnable_array;
  }

  // GET SYSTEM DATA

  // GET DARK LOGO
   public function get_logo_dark($type = "")

  {
    if ($type == 'small') {
      if (file_exists('uploads/system/logo/logo-dark-sm.png')) {
        return base_url('uploads/system/logo/logo-dark-sm.png');
      } else {
        return base_url('uploads/system/logo/logo-dark-sm.svg');
      }
    } elseif($type == 'white'){
      if (file_exists('uploads/system/logo/logo-light.png')) {
        return base_url('uploads/system/logo/logo-light.png');
      } else {
        return base_url('uploads/system/logo/logo-light.svg');
      }

    }else {
      if (file_exists('uploads/system/logo/logo-dark.png')) {
        return base_url('uploads/system/logo/logo-dark.png');
      } else {
        return base_url('uploads/system/logo/logo-dark.svg');
      }
    }

  }

  // GET LIGHT LOGO
  public function get_logo_light($type = "")
  {
    if ($type == 'small') {
      if (file_exists('uploads/system/logo/logo-light-sm.png')) {
        return base_url('uploads/system/logo/logo-light-sm.png');
      } else {
        return base_url('uploads/system/logo/logo-light-sm.svg');
      }

    } else {
      if (file_exists('uploads/system/logo/logo-light.png')) {
        return base_url('uploads/system/logo/logo-light.png');
      } else {
        return base_url('uploads/system/logo/logo-light.svg');
      }
    }
  }

  // GET FAVICON
  public function get_favicon()
  {
    if (file_exists('uploads/system/logo/favicon.png')) {
      return base_url('uploads/system/logo/favicon.png');
    } else {
      return base_url('uploads/system/logo/favicon.svg');
    }
  }

  public function get_logo_school($school_id)
  {
    if (file_exists('uploads/schools/' . $school_id . '.jpg')) {
      // die('uploads/schools/' . $school_id . '.jpg');
      return base_url('uploads/schools/' . $school_id . '.jpg');
    } else {
      return base_url('uploads/schools/placeholder.jpg');
    }
  }
}






