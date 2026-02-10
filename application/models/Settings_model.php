<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
 *  @author   : Creativeitem
 *  date      : November, 2019
 *  Ekattor School Management System With Addons
 *  http://codecanyon.net/user/Creativeitem
 *  http://support.creativeitem.com
 */

class Settings_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  public function update_system_settings()
  {
    $data['system_name'] = htmlspecialchars($this->input->post('system_name'));
    $data['system_email'] = htmlspecialchars($this->input->post('system_email'));
    $data['system_title'] = htmlspecialchars($this->input->post('system_title'));
    $data['phone'] = htmlspecialchars($this->input->post('phone'));
    $data['purchase_code'] = htmlspecialchars($this->input->post('purchase_code'));
    $data['address'] = htmlspecialchars($this->input->post('address'));
    // $data['fax'] = htmlspecialchars($this->input->post('fax'));
    $data['footer_text'] = htmlspecialchars($this->input->post('footer_text'));
    $data['footer_link'] = htmlspecialchars($this->input->post('footer_link'));
    $data['timezone'] = htmlspecialchars($this->input->post('timezone'));
    $data['youtube_api_key'] = htmlspecialchars($this->input->post('youtube_api_key'));
    $data['vimeo_api_key'] = htmlspecialchars($this->input->post('vimeo_api_key'));
    $this->db->where('id', 1);
    $this->db->update('settings', $data);
    $response = array(
      'status' => true,
      'notification' => get_phrase('system_settings_updated_successfully')
    );
    return json_encode($response);
  }

  public function last_updated_attendance_data()
  {
    $data['date_of_last_updated_attendance'] = strtotime(date('d-m-Y H:i:s'));
    $this->db->where('id', 1);
    $this->db->update('settings', $data);
  }


  public function update_system_logo() {
    
    // Définition du type MIME pour les fichiers SVG
    $svg_type = 'image/svg+xml';

    // Handle Dark Logo
    if ($_FILES['dark_logo']['name'] != "") {// Vérifie si un fichier a été uploadé
        $file_type = mime_content_type($_FILES['dark_logo']['tmp_name']); //Détecte le type MIME du fichier
        $destination = 'uploads/system/logo/logo-dark';// Définition du chemin de stockage

        // Si le fichier est un SVG
        if ($file_type === $svg_type) {
           // Supprime l'ancienne version PNG s'il existe
            if (file_exists($destination . '.png')) unlink($destination . '.png');
            // Sauvegarde le nouveau fichier en tant que SVG
            move_uploaded_file($_FILES['dark_logo']['tmp_name'], $destination . '.svg');

        } else {// Si le fichier n'est pas un SVG (donc PNG par défaut)
          // Supprime l'ancienne version SVG s'il existe
            if (file_exists($destination . '.svg')) unlink($destination . '.svg');
            // Sauvegarde le nouveau fichier en tant que PNG
            move_uploaded_file($_FILES['dark_logo']['tmp_name'], $destination . '.png');
        }
    }

    // Handle Light Logo
    if ($_FILES['light_logo']['name'] != "") {
        $file_type = mime_content_type($_FILES['light_logo']['tmp_name']);
        $destination = 'uploads/system/logo/logo-light';

        if ($file_type === $svg_type) {
            if (file_exists($destination . '.png')) unlink($destination . '.png');
            move_uploaded_file($_FILES['light_logo']['tmp_name'], $destination . '.svg');
        } else {
            if (file_exists($destination . '.svg')) unlink($destination . '.svg');
            move_uploaded_file($_FILES['light_logo']['tmp_name'], $destination . '.png');
        }
    }

    // Handle Small Logo
    if ($_FILES['small_logo']['name'] != "") {
        $file_type = mime_content_type($_FILES['small_logo']['tmp_name']);
        $destination = 'uploads/system/logo/logo-light-sm';

        if ($file_type === $svg_type) {
            if (file_exists($destination . '.png')) unlink($destination . '.png');
            move_uploaded_file($_FILES['small_logo']['tmp_name'], $destination . '.svg');
        } else {
            if (file_exists($destination . '.svg')) unlink($destination . '.svg');
            move_uploaded_file($_FILES['small_logo']['tmp_name'], $destination . '.png');
        }
    }

    // Handle Favicon
    if ($_FILES['favicon']['name'] != "") {
        $file_type = mime_content_type($_FILES['favicon']['tmp_name']);
        $destination = 'uploads/system/logo/favicon';

        if ($file_type === $svg_type) {
            if (file_exists($destination . '.png')) unlink($destination . '.png');
            move_uploaded_file($_FILES['favicon']['tmp_name'], $destination . '.svg');
        } else {
            if (file_exists($destination . '.svg')) unlink($destination . '.svg');
            move_uploaded_file($_FILES['favicon']['tmp_name'], $destination . '.png');
        }
    }
    $response = array(
        'status' => true,// Indique que l'opération a réussi
        'notification' => get_phrase('logo_updated_successfully') // Message de confirmation
    );
    return json_encode($response);// Retourne la réponse au format JSON
}

  // SCHOOL SETTINGS
  public function get_current_school_data()
  {
    return $this->db->get_where('schools', array('id' => school_id()))->row_array();
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
    return $this->db->get_where('settings_school', array('school_id' => school_id()))->row_array();
  }
  public function get_settings_school_data($school_id)
  {
    return $this->db->get_where('settings_school', array('school_id' => $school_id))->row_array();
  }
  public function update_current_school_settings()
  {
    $schoolId = school_id();
    
    $data['name'] = htmlspecialchars($this->input->post('school_name'));
    $data['phone'] = htmlspecialchars($this->input->post('phone'));
    $data['Rue'] = htmlspecialchars($this->input->post('communityStreet'));
    $data['Numero'] = htmlspecialchars($this->input->post('communityNumber'));
    $data['Ville'] = htmlspecialchars($this->input->post('communityCity'));
    $data['Codepostal'] = htmlspecialchars($this->input->post('communityPostalCode'));
    $data['description'] = htmlspecialchars($this->input->post('description'));
    $data['access'] = htmlspecialchars($this->input->post('access'));
    // $data['category'] was being overwritten. Assuming 'category' input is for school category
    $data['category'] = htmlspecialchars_decode($this->input->post('category'));
    
    // Validate Tax Residence and prepare country code
    $tax_residence = htmlspecialchars_decode($this->input->post('tax_residence'));
  
    // Validation Tax Residence
    if (!in_array($tax_residence, ['MA', 'UAE','AE'])) {
        log_message('error', 'Invalid Tax Residence value: ' . $tax_residence);
        return json_encode(['status' => false, 'notification' => 'Invalid Tax Residence value']);
    }
    
    // Set country code based on tax residence (or use tax residence directly if it IS the code)
    $country_code = $tax_residence;
    $data['country'] = $country_code;

    $this->db->where('id', $schoolId);
    $this->db->update('schools', $data);

    // ----------------- Upload logo (compressé) -----------------
    if(isset($_FILES['school_image']['name']) && $_FILES['school_image']['name'] != '' && $_FILES['school_image']['error'] === UPLOAD_ERR_OK) {
        $logo_path = 'uploads/schools/';
        if(!is_dir($logo_path)){
            mkdir($logo_path, 0777, true);
        }
        
        // Compresser et sauvegarder le logo (512x512 recommandé, qualité 90%)
        $logo_result = $this->compress_and_save_image(
            $_FILES['school_image']['tmp_name'],
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
    if(isset($_FILES['school_cover']['name']) && $_FILES['school_cover']['name'] != '' && $_FILES['school_cover']['error'] === UPLOAD_ERR_OK) {
        $cover_path = 'uploads/communityCover/';
        if(!is_dir($cover_path)){
            mkdir($cover_path, 0777, true);
        }
        
        // Compresser et sauvegarder la cover (1920x600 recommandé, qualité 90%)
        $cover_result = $this->compress_and_save_image(
            $_FILES['school_cover']['tmp_name'],
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
    $data_settings_school['type'] = htmlspecialchars_decode($this->input->post('i_am'));
    $data_settings_school['num_vat'] = htmlspecialchars_decode($this->input->post('num_vat'));


    
    // Gestion de la suppression du document
    if ($this->input->post('delete_tax_document') == '1') {
        // Récupérer le nom du fichier actuel
        $current_settings = $this->db->get_where('settings_school', array('school_id' => $schoolId))->row_array();
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
    if (isset($_FILES['tax_document']) && $_FILES['tax_document']['error'] === UPLOAD_ERR_OK) {
        $allowed_extensions = ['pdf', 'jpg', 'png', 'jpeg'];
        $file_ext = strtolower(pathinfo($_FILES['tax_document']['name'], PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_extensions)) {
            log_message('error', 'Invalid file extension: ' . $file_ext);
            return json_encode(['status' => false, 'notification' => 'Invalid file type. Only PDF, JPG, and PNG are allowed.']);
        }

        // Vérifier la taille du fichier (4 Mo max)
        $max_file_size = 4 * 1024 * 1024; // 4 Mo en bytes
        if ($_FILES['tax_document']['size'] > $max_file_size) {
            log_message('error', 'File too large: ' . $_FILES['tax_document']['size']);
            return json_encode(['status' => false, 'notification' => 'File is too large. Maximum size is 4 MB.']);
        }

        // Supprimer l'ancien fichier s'il existe
        $current_settings = $this->db->get_where('settings_school', array('school_id' => $schoolId))->row_array();
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

        $upload_path = $upload_path . $file_name;

        if (!move_uploaded_file($_FILES['tax_document']['tmp_name'], $upload_path)) {
            log_message('error', 'Failed to move uploaded file to ' . $upload_path);
            return json_encode(['status' => false, 'notification' => 'Failed to upload the file.']);
        }

        $data_settings_school['file'] = $file_name;
    } 
    // else {
    //     log_message('error', 'File upload error or no file uploaded.');
    //     return json_encode(['status' => false, 'notification' => 'No file uploaded or upload error.']);
    // }

    $this->db->where('school_id', $schoolId);
    $this->db->update('settings_school', $data_settings_school);

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
    $current_settings = $this->db->get_where('settings_school', array('school_id' => $schoolId))->row_array();
    
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
        $this->db->where('school_id', $schoolId);
        $this->db->update('settings_school', array('file' => NULL));
        
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
      $this->db->where('school_id', $schoolId);
      $this->db->update('settings_school', array('file' => NULL));
      
      return json_encode([
        'status' => true,
        'notification' => get_phrase('Document reference removed successfully')
      ]);
    }
  }

  // PAYMENT SETTINGS
  public function update_system_currency_settings()
  {
    $data['system_currency'] = htmlspecialchars($this->input->post('system_currency'));
    $data['currency_position'] = htmlspecialchars($this->input->post('currency_position'));

    $user_id =  $this->session->userdata('user_id');
    if (strtolower($this->db->get_where('users', array('id' => $user_id))->row('role')) == 'admin'){
          $this->db->where('school_id', school_id());
          $this->db->update('settings_school', $data);
    }else{
          $this->db->where('id', 1);
          $this->db->update('settings_school', $data);

    }

    $response = array(
      'status' => true,
      'notification' => get_phrase('system_settings_updated_successfully')
    );
  // ----------------- Réponse -----------------
    $response = [
        'status' => true,
        'notification' => get_phrase('school_settings_updated_successfully')
    ];
    return json_encode($response);
  }
    public function toggle_community_etat()
  {
    $is_public = intval($this->input->post('etat'));
    // Convert: toggle sends 1=public, 0=private
    // access column: 0=public, 1=private
    $access = ($is_public == 1) ? 0 : 1;

    $user_id = $this->session->userdata('user_id');
    if (strtolower($this->db->get_where('users', array('id' => $user_id))->row('role')) == 'admin') {
        $target_school_id = school_id();
    } else {
        $target_school_id = 1;
    }

    $data = array('access' => $access);

    // If switching to private (access = 1), also reset price to 0
    if ($access == 1) {
        $data['price'] = 0;
    }

    $this->db->where('id', $target_school_id);
    $this->db->update('schools', $data);

    $response = array(
      'status' => true,
      'notification' => get_phrase('community_state_updated_successfully')
    );
    return json_encode($response);
  }

    public function update_system_price()
  {
    // Backend protection: check if community is private (access = 1)
    $user_id = $this->session->userdata('user_id');
    if (strtolower($this->db->get_where('users', array('id' => $user_id))->row('role')) == 'admin') {
        $target_school_id = school_id();
    } else {
        $target_school_id = 1;
    }

    $school = $this->db->get_where('schools', array('id' => $target_school_id))->row_array();

    // Block price update if community is private
    if (isset($school['access']) && $school['access'] == 1) {
        $response = array(
          'status' => false,
          'notification' => get_phrase('you_cannot_monetize_a_private_community')
        );
        return false;
    }

    $data['price'] = htmlspecialchars($this->input->post('price_community'));

    $this->db->where('id', $target_school_id);
    $this->db->update('schools', $data);

    $response = array(
      'status' => true,
      'notification' => get_phrase('system_settings_updated_successfully')
    );
    return json_encode($response);
  }
  public function update_system_vat()
  {
    
    $data['vat_enabled'] = htmlspecialchars($this->input->post('vat_applicable'));
    $data['vat_rate'] = htmlspecialchars($this->input->post('vat_rate'));
   
    $user_id =  $this->session->userdata('user_id');
    if (strtolower($this->db->get_where('users', array('id' => $user_id))->row('role')) == 'admin'){
          $this->db->where('school_id', school_id());
          $this->db->update('settings_school', $data);
    }else{
          $this->db->where('id', 1);
          $this->db->update('settings_school', $data);

    }

    $response = array(
      'status' => true,
      'notification' => get_phrase('system_settings_updated_successfully')
    );
    return json_encode($response);
  }
  public function update_paypal_settings()
  {
    $paypal_info = array();
    $CI = &get_instance();
    $CI->load->database();

    $paypal['paypal_active'] = htmlspecialchars($this->input->post('paypal_active'));
    $paypal['paypal_mode'] = htmlspecialchars($this->input->post('paypal_mode'));
    $paypal['paypal_client_id_sandbox'] = htmlspecialchars($this->input->post('paypal_client_id_sandbox'));
    $paypal['paypal_client_id_production'] = htmlspecialchars($this->input->post('paypal_client_id_production'));
    $paypal['paypal_currency'] = htmlspecialchars($this->input->post('paypal_currency'));

    array_push($paypal_info, $paypal);

    $data['value'] = json_encode($paypal_info);
    $this->db->where('key', 'paypal_settings');
    $this->db->where('school_id', $CI->session->userdata('school_id'));
    $this->db->update('payment_settings', $data);

    $response = array(
      'status' => true,
      'notification' => get_phrase('paypal_settings_updated_successfully')
    );
    return json_encode($response);
  }

  public function update_stripe_settings()
  {
    $stripe_info = array();
    $CI = &get_instance();
    $CI->load->database();
    $stripe['stripe_active'] = htmlspecialchars($this->input->post('stripe_active'));
    $stripe['stripe_mode'] = htmlspecialchars($this->input->post('stripe_mode'));
    $stripe['stripe_test_secret_key'] = htmlspecialchars($this->input->post('stripe_test_secret_key'));
    $stripe['stripe_test_public_key'] = htmlspecialchars($this->input->post('stripe_test_public_key'));
    $stripe['stripe_live_secret_key'] = htmlspecialchars($this->input->post('stripe_live_secret_key'));
    $stripe['stripe_live_public_key'] = htmlspecialchars($this->input->post('stripe_live_public_key'));
    $stripe['stripe_currency'] = htmlspecialchars($this->input->post('stripe_currency'));

    array_push($stripe_info, $stripe);

    $data['value'] = json_encode($stripe_info);
    $this->db->where('key', 'stripe_settings');
    $this->db->where('school_id', $CI->session->userdata('school_id'));
    $this->db->update('payment_settings', $data);

    $response = array(
      'status' => true,
      'notification' => get_phrase('paypal_settings_updated_successfully')
    );
    return json_encode($response);
  }

  // UPDATE SMTP CREDENTIALS
  public function update_smtp_settings()
  {
    if ($this->input->post('mail_sender') == 'php_mailer') {
      if (empty($this->input->post('smtp_secure')) || empty($this->input->post('smtp_set_from')) || empty($this->input->post('smtp_show_error'))) {
        $response = array(
          'status' => false,
          'notification' => get_phrase('please_fill_all_the_fields')
        );
        return json_encode($response);
      }
    }

    $data['mail_sender'] = htmlspecialchars($this->input->post('mail_sender'));
    $data['smtp_protocol'] = htmlspecialchars($this->input->post('smtp_protocol'));
    $data['smtp_host'] = htmlspecialchars($this->input->post('smtp_host'));
    $data['smtp_crypto'] = htmlspecialchars($this->input->post('smtp_crypto'));
    $data['smtp_username'] = htmlspecialchars($this->input->post('smtp_username'));
    $data['smtp_password'] = htmlspecialchars($this->input->post('smtp_password'));
    $data['smtp_port'] = htmlspecialchars($this->input->post('smtp_port'));

    $data['smtp_secure'] = strtolower($this->input->post('smtp_secure'));
    $data['smtp_set_from'] = htmlspecialchars($this->input->post('smtp_set_from'));
    $data['smtp_show_error'] = htmlspecialchars($this->input->post('smtp_show_error'));

    if ($this->db->get('smtp_settings')->num_rows() > 0) {
      $this->db->where('id', 1);
      $this->db->update('smtp_settings', $data);
    } else {
      $this->db->insert('smtp_settings', $data);
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
  //   $this->db->distinct('name');
  //   $this->db->select('name');
  //   return $this->db->get('language')->result_array();
  // }

  public function create_language()
  {
    saveDefaultJSONFile(trimmer($this->input->post('language')));
    $response = array(
      'status' => true,
      'notification' => get_phrase('language_added_successfully')
    );
    return json_encode($response);
  }

  public function update_language($param1 = "")
  {
    if (file_exists('application/language/' . $param1 . '.json')) {
      unlink('application/language/' . $param1 . '.json');
    }
    saveDefaultJSONFile(trimmer($this->input->post('language')));
    $response = array(
      'status' => true,
      'notification' => get_phrase('language_added_successfully')
    );
    return json_encode($response);
  }

  public function delete_language($param1 = "")
  {
    if (file_exists('application/language/' . $param1 . '.json')) {
      unlink('application/language/' . $param1 . '.json');
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
        $this->db->where('id', $user_id);
        $this->db->update('users', ['language' => $selected_language]);
    } else {
        $this->db->where('id', 1);
        $this->db->update('settings', ['language' => $selected_language]);
    }
    
    // Update session with the new language and lang_code
    $CI =& get_instance();
    $CI->session->set_userdata('language', $selected_language);
    
    // Set the language code for URL prefixes
    $lang_lower = strtolower($selected_language);
    $code = isset($lang_codes[$lang_lower]) ? $lang_codes[$lang_lower] : 'en';
    $CI->session->set_userdata('lang_code', $code);
  }
  function get_currencies()
  {
    $this->db->where('payumoney_supported', 1);
    return $this->db->get('currencies')->result_array();
  }

  function get_paypal_supported_currencies()
  {
    $this->db->where('paypal_supported', 1);
    return $this->db->get('currencies')->result_array();
  }

  function get_stripe_supported_currencies()
  {
    $this->db->where('stripe_supported', 1);
    return $this->db->get('currencies')->result_array();
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

    if (count($response['verify-purchase']) > 0) {

      //print_r($response);
      $item_name = $response['verify-purchase']['item_name'];
      $purchase_time = $response['verify-purchase']['created_at'];
      $customer = $response['verify-purchase']['buyer'];
      $licence_type = $response['verify-purchase']['licence'];
      $support_until = $response['verify-purchase']['supported_until'];
      $customer = $response['verify-purchase']['buyer'];

      $purchase_date = date("d M, Y", strtotime($purchase_time));

      $todays_timestamp = strtotime(date("d M, Y"));
      $support_expiry_timestamp = strtotime($support_until);

      $support_expiry_date = date("d M, Y", $support_expiry_timestamp);

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






