<?php

namespace App\Models;

use CodeIgniter\Model;

class Frontend_model extends Model {
    protected $table            = 'frontend';
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
    protected $school_id;
    protected $active_session;
    protected $security;

    public function __construct()
    {
        parent::__construct();
        $this->request = \Config\Services::request();
        $this->session = \Config\Services::session();
        $security = \CodeIgniter\Config\Services::security();
        $this->security = new \App\View\SecurityCompat($security);
        $this->school_id = function_exists('school_id') ? school_id() : null;
        $this->active_session = function_exists('active_session') ? active_session() : null;
    }

    public function __get($name)
    {
        if ($name === 'db') {
            return \Config\Database::connect();
        }
        return parent::__get($name);
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

  private function validateImageUpload(string $field): array
  {
    $file = $this->uploadedFile($field);
    
    if (!$file || $file->getError() !== UPLOAD_ERR_OK) {
      return ['valid' => false, 'message' => get_phrase('file_upload_error')];
    }

    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $mimeType = $file->getClientMimeType();
    
    if (!in_array($mimeType, $allowedTypes)) {
      return ['valid' => false, 'message' => get_phrase('invalid_file_type_only_jpg_png_gif_webp_allowed')];
    }

    $maxSize = 5 * 1024 * 1024;
    if ($file->getSize() > $maxSize) {
      return ['valid' => false, 'message' => get_phrase('file_too_large_max_5mb')];
    }

    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file->getClientName(), PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowedExts)) {
      return ['valid' => false, 'message' => get_phrase('invalid_file_extension_only_jpg_png_gif_webp_allowed')];
    }

    $imageInfo = @getimagesize($file->getTempName());
    if ($imageInfo === false) {
      return ['valid' => false, 'message' => get_phrase('invalid_image_file')];
    }

    if ($imageInfo[0] < 10 || $imageInfo[1] < 10) {
      return ['valid' => false, 'message' => get_phrase('image_too_small_minimum_10x10')];
    }

    if ($imageInfo[0] > 5000 || $imageInfo[1] > 5000) {
      return ['valid' => false, 'message' => get_phrase('image_too_large_maximum_5000x5000')];
    }

    if (!in_array($imageInfo['mime'], $allowedTypes)) {
      return ['valid' => false, 'message' => get_phrase('mime_type_mismatch')];
    }

    return ['valid' => true];
  }

  /**
   * Compresse et redimensionne une image uploadée
   * Préserve la qualité tout en optimisant la taille du fichier
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
      if (!file_exists($source_path)) {
          log_message('error', 'Image compression: Source file not found - ' . $source_path);
          return false;
      }

      if (!extension_loaded('gd')) {
          log_message('error', 'Image compression: GD extension not available');
          return false;
      }

      $image_info = @getimagesize($source_path);
      if ($image_info === false) {
          log_message('error', 'Image compression: Unable to get image info - ' . $source_path);
          return false;
      }

      $original_width = $image_info[0];
      $original_height = $image_info[1];
      $mime_type = $image_info['mime'];

      if ($original_width < 1 || $original_height < 1) {
          log_message('error', 'Image compression: Invalid image dimensions');
          return false;
      }

      $source_image = $this->create_image_from_file($source_path, $mime_type);
      
      if ($source_image === false) {
          log_message('error', 'Image compression: Failed to create image resource');
          return false;
      }

      list($new_width, $new_height) = $this->calculate_dimensions($original_width, $original_height, $max_width, $max_height);

      $destination_image = $this->create_destination_image($source_image, $original_width, $original_height, $new_width, $new_height, $mime_type);

      if ($destination_image === false) {
          imagedestroy($source_image);
          return false;
      }

      $final_path = $destination_path . '.jpg';
      $save_result = $this->save_optimized_jpeg($destination_image, $final_path, $quality);

      imagedestroy($source_image);
      imagedestroy($destination_image);

      if (!$save_result) {
          if (file_exists($final_path)) {
              @unlink($final_path);
          }
          log_message('error', 'Image compression: Failed to save image - ' . $final_path);
          return false;
      }

      $this->log_compression_result($source_path, $final_path, $original_width, $original_height, $new_width, $new_height);
      return $final_path;
  }

  private function create_image_from_file($source_path, $mime_type)
  {
      $source_image = false;

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
      }

      if ($source_image === false) {
          $image_data = @file_get_contents($source_path);
          if ($image_data !== false) {
              $source_image = @imagecreatefromstring($image_data);
          }
      }

      return $source_image;
  }

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

  private function create_destination_image($source_image, $original_width, $original_height, $new_width, $new_height, $mime_type)
  {
      $destination_image = @imagecreatetruecolor($new_width, $new_height);

      if ($destination_image === false) {
          log_message('error', 'Image compression: Failed to create destination image');
          return false;
      }

      $white = imagecolorallocate($destination_image, 255, 255, 255);
      imagefilledrectangle($destination_image, 0, 0, $new_width, $new_height, $white);
      imageinterlace($destination_image, true);

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

  private function save_optimized_jpeg($image, $path, $quality)
  {
      $result = @imagejpeg($image, $path, $quality);
      
      if ($result && file_exists($path)) {
          $file_size = filesize($path);
          $max_size = 500 * 1024;
          $min_quality = 70;
          
          while ($file_size > $max_size && $quality > $min_quality) {
              $quality -= 5;
              $result = @imagejpeg($image, $path, $quality);
              if ($result && file_exists($path)) {
                  $file_size = filesize($path);
              } else {
                  break;
              }
          }
      }

      return $result && file_exists($path);
  }

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

  private function format_bytes($bytes)
  {
      $units = ['B', 'KB', 'MB', 'GB'];
      $bytes = max($bytes, 0);
      $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
      $pow = min($pow, count($units) - 1);
      $bytes /= pow(1024, $pow);
      return round($bytes, 2) . ' ' . $units[$pow];
  }

  // get noticeboard
  function get_frontend_noticeboard()
  {
    return \db()->table('noticeboard')
      ->where('show_on_website', 1)
      ->orderBy('create_timestamp', 'DESC')
      ->get()
      ->getResultArray();
  }

  function get_frontend_recent_noticeboard()
  {
    return \db()->table('noticeboard')
      ->where('show_on_website', 1)
      ->orderBy('create_timestamp', 'DESC')
      ->limit(4)
      ->get()
      ->getResultArray();
  }

  function get_frontend_all_events()
  {
    return \db()->table('frontend_events')
      ->where('status', 1)
      ->orderBy('timestamp', 'DESC')
      ->get()
      ->getResultArray();
  }

  function get_frontend_upcoming_events()
  {
    return \db()->table('frontend_events')
      ->where('status', 1)
      ->where('school_id', $this->get_active_school_id())
      ->where('timestamp >', time())
      ->limit(4)
      ->get()
      ->getResultArray();
  }

  function get_frontend_teachers()
  {
    return \db()->table('teacher')
      ->where('show_on_website', 1)
      ->get()
      ->getResultArray();
  }

  function get_frontend_notice_by_id($notice_id)
  {
    return \db()->table('noticeboard')
      ->where('id', $notice_id)
      ->get()
      ->getResultArray();
  }

  // get all events
  function get_events()
  {
    return \db()->table('frontend_events')
      ->orderBy('timestamp', 'DESC')
      ->get()
      ->getResultArray();
  }
  // add event
  function event_create()
  {
    $data['title'] = html_escape($this->request->getPost('title'));
    $data['timestamp'] = strtotime(html_escape($this->request->getPost('timestamp')));
    $data['status'] = html_escape($this->request->getPost('status'));
    $data['school_id'] = school_id();
    $data['created_by'] = session()->get('user_id');
    \db()->table('frontend_events')->insert($data);

    $response = array(
      'status' => true,
      'notification' => get_phrase('event_added')
    );
    return json_encode($response);
  }
  // edit event
  function event_update($event_id)
  {
    $data['title'] = html_escape($this->request->getPost('title'));
    $data['timestamp'] = strtotime(html_escape($this->request->getPost('timestamp')));
    $data['status'] = html_escape($this->request->getPost('status'));

    \db()->table('frontend_events')->where('frontend_events_id', $event_id)->update($data);

    $response = array(
      'status' => true,
      'notification' => get_phrase('event_added')
    );
    return json_encode($response);
  }
  // delete event
  function event_delete($event_id)
  {
    \db()->table('frontend_events')->where('frontend_events_id', $event_id)->delete();

    $response = array(
      'status' => true,
      'notification' => get_phrase('event_deleted')
    );
    return json_encode($response);
  }

  // news
  function get_news()
  {
    return \db()->table('frontend_news')
      ->orderBy('date_added', 'DESC')
      ->get()
      ->getResultArray();
  }

  function add_news()
  {
    $data['title'] = html_escape($this->request->getPost('title'));
    $data['description'] = html_escape($this->request->getPost('description'));
    $data['date_added'] = strtotime(html_escape($this->request->getPost('date')));
    if ($this->isUploadOk('news_image')) {
      $newsImage = $this->uploadedFile('news_image');
      $data['image'] = $newsImage->getClientName();
      $newsImage->move('uploads/frontend/news_image', $data['image'], true);
    }
    \db()->table('frontend_news')->insert($data);
  }

  function delete_news($news_id)
  {
    // delete the news image if exists
    $news = \db()->table('frontend_news')->where('frontend_news_id', $news_id)->get()->getRowArray();
    $news_image = $news['image'] ?? null;
    if ($news_image != NULL) {
      if (file_exists('uploads/frontend/news_image/' . $news_image)) {
        unlink('uploads/frontend/news_image/' . $news_image);
      }
    }
    // delete the db entry
    \db()->table('frontend_news')->delete(['frontend_news_id' => $news_id]);
  }

  // gallery
  function get_gallaries()
  {
    return \db()->table('frontend_gallery')
      ->orderBy('date_added', 'DESC')
      ->get()
      ->getResultArray();
  }

  function get_gallery_info_by_id($gallery_id)
  {
    return \db()->table('frontend_gallery')
      ->where('frontend_gallery_id', $gallery_id)
      ->get()
      ->getResultArray();
  }

  function add_frontend_gallery()
  {

    $data['title'] = html_escape($this->request->getPost('title'));
    $data['description'] = html_escape($this->request->getPost('description'));
    $data['show_on_website'] = htmlspecialchars($this->request->getPost('show_on_website'));
    $data['school_id'] = $this->school_id;
    $data['date_added'] = strtotime(html_escape($this->request->getPost('date_added')));

    if ($this->isUploadOk('cover_image')) {
      $data['image'] = random(15) . '.jpg';
      $this->uploadedFile('cover_image')->move('uploads/images/gallery_cover', $data['image'], true);
    }
    \db()->table('frontend_gallery')->insert($data);
    $response = array(
      'status' => true,
      'notification' => get_phrase('gallery_added')
    );
    return json_encode($response);
  }

  function update_frontend_gallery($gallery_id)
  {
    $data['title'] = html_escape($this->request->getPost('title'));
    $data['description'] = html_escape($this->request->getPost('description'));
    $data['show_on_website'] = htmlspecialchars($this->request->getPost('show_on_website'));

    if ($this->isUploadOk('cover_image')) {
      $data['image'] = random(15) . '.jpg';
      $this->uploadedFile('cover_image')->move('uploads/images/gallery_cover', $data['image'], true);
    }
    \db()->table('frontend_gallery')->where('frontend_gallery_id', $gallery_id)->update($data);
    $response = array(
      'status' => true,
      'notification' => get_phrase('gallery_updated')
    );
    return json_encode($response);
  }

  public function delete_frontend_gallery($gallery_id = "")
  {
    \db()->table('frontend_gallery')->where('frontend_gallery_id', $gallery_id)->delete();

    $response = array(
      'status' => true,
      'notification' => get_phrase('gallery_deleted')
    );
    return json_encode($response);
  }

  // Add Image in gallery
  public function upload_gallery_photo($gallery_id)
  {
    if ($this->isUploadOk('gallery_photo')) {
      $data['frontend_gallery_id'] = $gallery_id;
      $data['image'] = random(20) . '.jpg';
      $this->uploadedFile('gallery_photo')->move('uploads/images/gallery_images', $data['image'], true);

      \db()->table('frontend_gallery_image')->insert($data);

      $response = array(
        'status' => true,
        'notification' => get_phrase('gallery_image_has_been_added_successfully')
      );
    } else {
      $response = array(
        'status' => false,
        'notification' => get_phrase('no_image_found')
      );
    }
    return json_encode($response);
  }

  //DELETE PHOTO FROM GALLERY
  public function delete_gallery_photo($gallery_photo_id)
  {
    $gallery_photo_previous_data = \db()->table('frontend_gallery_image')->where('frontend_gallery_image_id', $gallery_photo_id)->get()->getRowArray();
    \db()->table('frontend_gallery_image')->delete(['frontend_gallery_image_id' => $gallery_photo_id]);
    $this->remove_image('gallery_images', $gallery_photo_previous_data['image'] ?? '');
    $response = array(
      'status' => true,
      'notification' => get_phrase('gallery_photo_deleted')
    );
    return json_encode($response);
  }
  function add_gallery_images($gallery_id)
  {
    $files = $this->request->getFiles();
    $galleryImages = $files['gallery_images'] ?? [];
    if (!is_array($galleryImages)) {
      $galleryImages = [$galleryImages];
    }
    foreach ($galleryImages as $file) {
      if ($file && $file->getError() === UPLOAD_ERR_OK && $file->getClientName() !== '') {
        $file->move('uploads/frontend/gallery_images', $file->getClientName(), true);
        $data['frontend_gallery_id'] = $gallery_id;
        $data['image'] = $file->getClientName();
        \db()->table('frontend_gallery_image')->insert($data);
      }
    }
  }

  function get_frontend_gallery_images_limited($gallery_id)
  {
    return \db()->table('frontend_gallery_image')
      ->where('frontend_gallery_id', $gallery_id)
      ->orderBy('frontend_gallery_image_id', 'DESC')
      ->limit(4)
      ->get()
      ->getResultArray();
  }

  function delete_gallery_image($gallery_image_id)
  {
    $image = \db()->table('frontend_gallery_image')->where(
      ['frontend_gallery_image_id' => $gallery_image_id]
    )->get()->getRow()->image;
    if (file_exists('uploads/frontend/gallery_images/' . $image)) {
      unlink('uploads/frontend/gallery_images/' . $image);
    }
    \db()->table('frontend_gallery_image')->where('frontend_gallery_image_id', $gallery_image_id)->delete();
  }

  function get_gallery_images($gallery_id)
  {
    return \db()->table('frontend_gallery_image')
      ->where('frontend_gallery_id', $gallery_id)
      ->orderBy('frontend_gallery_image_id', 'DESC')
      ->get()
      ->getResultArray();
  }

  //FRONTEND GALLERY
  public function get_photos_by_gallery_id($frontend_gallery_id = "")
  {
    return \db()->table('frontend_gallery_image')
      ->where('frontend_gallery_id', $frontend_gallery_id)
      ->get()
      ->getResultArray();
  }

  public function get_gallery_image($image = "")
  {
    if (file_exists('uploads/images/gallery_images/' . $image))
      return base_url() . 'uploads/images/gallery_images/' . $image;
    else
      return base_url() . 'uploads/images/gallery_images/placeholder.png';
  }

  // get general settings
  function get_frontend_general_settings($type = '')
  {
    $result = \db()->table('frontend_settings')->where(['type' => $type])->get()->getRow()->description;
    return $result == null ? '' : $result;
  }

  // update terms and conditions
  function update_terms_and_conditions()
  {
    $data['terms_conditions'] = html_escape($this->request->getPost('terms_and_conditions'));
    \db()->table('frontend_settings')->where('id', 1)->update($data);

    $response = array(
      'status' => true,
      'notification' => get_phrase('updated')
    );
    return json_encode($response);
  }

  // update privacy policy
  function update_privacy_policy()
  {
    $data['privacy_policy'] = html_escape($this->request->getPost('privacy_policy'));
    \db()->table('frontend_settings')->where('id', 1)->update($data);

    $response = array(
      'status' => true,
      'notification' => get_phrase('updated')
    );
    return json_encode($response);
  }

  // update about us
  function update_about_us()
  {
    $data['about_us'] = html_escape($this->request->getPost('about_us'));
    \db()->table('frontend_settings')->where('id', 1)->update($data);

    if ($this->isUploadOk('about_us_image')) {
      $this->uploadedFile('about_us_image')->move('uploads/images/about_us', 'about-us.jpg', true);
    }

    $response = array(
      'status' => true,
      'notification' => get_phrase('updated')
    );
    return json_encode($response);
  }

  // send message from contact form
  public function send_contact_message()
{
    // Start output buffering to capture any unintended output
    ob_start();

    $validation = \Config\Services::validation();
    $validation->setRules([
      'first_name' => 'required|trim',
      'last_name'  => 'required|trim',
      'email'      => 'required|valid_email|trim',
      'comment'    => 'required|trim|min_length[5]',
      'phone'      => [
        'rules'  => 'permit_empty|trim|regex_match[/^\+[0-9]{10,15}$/]',
        'errors' => ['regex_match' => 'The Phone field must start with "+" followed by 10 to 15 digits.']
      ]
    ]);

    // Set JSON header
    header('Content-Type: application/json; charset=utf-8');

    // Check CSRF token
    if (!$this->security->csrf_verify()) {
        ob_end_clean();
        echo json_encode(['status' => 0, 'message' => get_phrase('Invalid CSRF token')]);
        exit;
    }

    // Run form validation
    if (!$validation->run($this->request->getPost())) {
        ob_end_clean();
        echo json_encode(['status' => 0, 'message' => strip_tags(implode(' ', $validation->getErrors()))]);
        exit;
    }

    // Get and sanitize input
    $first_name = html_escape($this->request->getPost('first_name'));
    $last_name = html_escape($this->request->getPost('last_name'));
    $email = html_escape($this->request->getPost('email'));
    $address = html_escape($this->request->getPost('address'));
    $phone = html_escape($this->request->getPost('phone'));
    $localisation = html_escape($this->request->getPost('localisation'));
    $comment = html_escape($this->request->getPost('comment'));

    // Determine salutation based on time
    $hour = date('H');
    if ($hour < 12) {
        $salutation = 'Good Morning';
    } elseif ($hour < 18) {
        $salutation = 'Good Afternoon';
    } else {
        $salutation = 'Good Evening';
    }

    // Build email content
    $msg = "<!DOCTYPE html>";
    $msg .= "<html lang='en'>";
    $msg .= "<head>";
    $msg .= "<meta charset='UTF-8'>";
    $msg .= "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    $msg .= "<style>";
    $msg .= "body { margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; }";
    $msg .= ".container { background: #ECECEC; }";
    $msg .= ".logo { text-align: center; margin-bottom: 20px; }";
    $msg .= ".content-table { width: 768px; margin: 0 auto; border-collapse: collapse; }";
    $msg .= ".header { background: #F67D34; border-radius: 15px 15px 0 0; padding: 15px; text-align: center; }";
    $msg .= ".header span { font-size: 20px; font-weight: bold; color: #fff; }";
    $msg .= ".body { background: #f0f7fa; padding: 20px; }";
    $msg .= ".body span { font-size: 14px; color: #000; line-height: 1.5; display: block; margin-bottom: 10px; }";
    $msg .= ".body a { color: #0047AB; text-decoration: underline; }";
    $msg .= ".footer { background: #F67D34; border-radius: 0 0 15px 15px; color: #fff; font-size: 14px; }";
    $msg .= ".footer table { width: 100%; }";
    $msg .= ".footer td { padding: 10px; }";
    $msg .= "@media only screen and (max-width: 600px) {";
    $msg .= ".content-table { width: 100% !important; }";
    $msg .= ".header span { font-size: 16px !important; }";
    $msg .= ".footer td { display: block; text-align: center !important; width: 80% !important; }";
    $msg .= "}";
    $msg .= "</style>";
    $msg .= "</head>";
    $msg .= "<body>";
    $msg .= "<div class='container'>";
    $msg .= "<table width='100%' cellpadding='30' cellspacing='0'>";
    $msg .= "<tr><td align='center'>";
    $msg .= "<div class='logo'><img src='https://wayo.academy/uploads/system/logo/logo-light.png?v=1' alt='Wayo Academy Logo' style='max-width: 150px; height: auto;'></div>";
    $msg .= "<table class='content-table' width='768' align='center' cellpadding='0' cellspacing='0'>";
    $msg .= "<tr>";
    $msg .= "<td class='header'><span>{$salutation} Support!</span></td>";
    $msg .= "</tr>";
    $msg .= "<tr>";
    $msg .= "<td class='body' style='padding: 20px;'>";
    $msg .= "<span><strong>From:</strong> {$first_name} {$last_name}</span>";
    $msg .= "<span style='margin-top: 20px;'><strong>Message:</strong> " . nl2br($comment) . "</span>";
    $msg .= "<span><strong>Email:</strong> <a href='mailto:{$email}'>{$email}</a></span>";
    if (!empty($phone)) {
        $msg .= "<span><strong>Phone:</strong> <a href='tel:{$phone}'>{$phone}</a></span>";
    }
    if (!empty($address)) {
        $msg .= "<span><strong>Postal address:</strong> {$address}</span>";
    }
    if (!empty($localisation)) {
        $msg .= "<span><strong>City / Country:</strong> {$localisation}</span>";
    }
    $msg .= "</td>";
    $msg .= "</tr>";
    $msg .= "<tr>";
    $msg .= "<td class='footer'>";
    $msg .= "<table width='100%' cellpadding='10' cellspacing='0'>";
    $msg .= "<tr>";
    $msg .= "<td width='200' style='text-align: left;'><a href='https://wayo.academy/home/contact#map' style='color: #fff; text-decoration: none;'>R320 Umm Hurair 2 Dubai UAE</a></td>";
    $msg .= "<td width='200' style='text-align: center;'>©2025 All the rights reserved to Wayo Academy</td>";
    $msg .= "<td width='200' style='text-align: right;'>Tel: <a href='tel:+971501548923' style='color: #fff; text-decoration: none;'>+971 50 154 8923</a></td>";
    $msg .= "</tr>";
    $msg .= "</table>";
    $msg .= "</td>";
    $msg .= "</tr>";
    $msg .= "</table>";
    $msg .= "</td></tr>";
    $msg .= "</table>";
    $msg .= "</div>";
    $msg .= "</body>";
    $msg .= "</html>";

    try {
        $receiver_email = get_settings('system_email');
        $this->email_model->contact_message_email($email, $receiver_email, $msg);
        ob_end_clean();
        echo json_encode(['status' => 1, 'message' => get_phrase('Your message has been sent successfully.')]);
        exit;
    } catch (Exception $e) {
        ob_end_clean();
        echo json_encode(['status' => 0, 'message' => get_phrase('Failed to send your message: ') . $e->getMessage()]);
        exit;
    }
}

  // update slider images
  function update_homepage_slider()
  {
    $current_images_json = get_frontend_settings('slider_images');
    $current_images = json_decode($current_images_json);
    $slider = array();
    for ($i = 0; $i < 3; $i++) {
      $image = $current_images[$i]->image;
      $data['title'] = html_escape($this->request->getPost('title_' . $i));
      $data['description'] = html_escape($this->request->getPost('description_' . $i));
      if ($this->isUploadOk('slider_image_' . $i)) {
        $sliderFile = $this->uploadedFile('slider_image_' . $i);
        $data['image'] = $sliderFile->getClientName();
        $sliderFile->move('uploads/images/slider', $data['image'], true);
      } else {
        $data['image'] = $image;
      }
      array_push($slider, $data);
    }

    $slider_data['slider_images'] = json_encode($slider);
    \db()->table('frontend_settings')->where('id', 1)->update($slider_data);

    $response = array(
      'status' => true,
      'notification' => get_phrase('updated')
    );
    return json_encode($response);
  }

  // update general settings
  function update_frontend_general_settings()
  {
    $links = array();
    $social['facebook'] = html_escape($this->request->getPost('facebook_link'));
    $social['twitter'] = html_escape($this->request->getPost('twitter_link'));
    $social['linkedin'] = html_escape($this->request->getPost('linkedin_link'));
    $social['google'] = html_escape($this->request->getPost('google_link'));
    $social['youtube'] = html_escape($this->request->getPost('youtube_link'));
    $social['instagram'] = html_escape($this->request->getPost('instagram_link'));
    array_push($links, $social);

    $data['social_links'] = json_encode($links);
    $data['website_title'] = htmlspecialchars($this->request->getPost('website_title'));
    $data['homepage_note_title'] = htmlspecialchars($this->request->getPost('homepage_note_title'));
    $data['homepage_note_description'] = htmlspecialchars($this->request->getPost('homepage_note_description'));
    $data['copyright_text'] = htmlspecialchars($this->request->getPost('copyright_text'));
    $updated = \db()->table('frontend_settings')->where('id', 1)->update($data);

    if ($this->isUploadOk('header_logo')) {
      $this->uploadedFile('header_logo')->move('uploads/system/logo', 'header-logo.png', true);
    }

    if ($this->isUploadOk('footer_logo')) {
      $this->uploadedFile('footer_logo')->move('uploads/system/logo', 'footer-logo.png', true);
    }

    $response = array(
      'status' => (bool) $updated,
      'notification' => get_phrase('general_settings_updated_successfully')
    );
    return json_encode($response);
  }

  // update general settings
  function other_settings_update()
  {
    if ($this->isUploadOk('login_banner')) {
      $this->uploadedFile('login_banner')->move('assets/backend/images', 'bg-auth.jpg', true);
    }

    $response = array(
      'status' => true,
      'notification' => get_phrase('other_settings_updated')
    );
    return json_encode($response);
  }

  function update_recaptcha_settings()
  {
    $data1['description'] = htmlspecialchars($this->request->getPost('recaptcha_status'));
    $data2['description'] = htmlspecialchars($this->request->getPost('recaptcha_sitekey'));
    $data3['description'] = htmlspecialchars($this->request->getPost('recaptcha_secretkey'));
    $ok1 = \db()->table('common_settings')->where('type', 'recaptcha_status')->update($data1);
    $ok2 = \db()->table('common_settings')->where('type', 'recaptcha_sitekey')->update($data2);
    $ok3 = \db()->table('common_settings')->where('type', 'recaptcha_secretkey')->update($data3);

    $response = array(
      'status' => (bool) ($ok1 && $ok2 && $ok3),
      'notification' => get_phrase('recaptcha_settings_updated')
    );
    return json_encode($response);
  }


  // MY CODE STARTS FROM HERE

  //GET ATIVE SCHOOL ID
  public function get_active_school_id()
  {
    $session_id = session()->get('active_school_id');
    if ($session_id && $this->is_valid_school($session_id)) {
      return $session_id;
    }

    $user_id = session()->get('user_id');
    if ($user_id) {
      $user = \db()->table('users')->select('school_id')->where('id', $user_id)->get()->getRow();
      if ($user && $user->school_id && $this->is_valid_school($user->school_id)) {
        session()->set('active_school_id', $user->school_id);
        return $user->school_id;
      }
    }

    if (addon_status('multi-school')) {
      $default = get_settings('school_id');
      if ($this->is_valid_school($default)) {
        session()->set('active_school_id', $default);
        return $default;
      }
    }
  }

  // Vérifie que l'école existe et est active
  private function is_valid_school($id)
  {
    if (!$id) return false;
    
    try {
      return \db()->table('schools')->where('id', $id)
        ->where('status', 1)
        ->where('Etat', 1)
        ->countAllResults() > 0;
    } catch (\Exception $e) {
      return true;
    }
  }
  // GET HEADER LOGO
  public function get_header_logo()
  {
    return base_url('uploads/system/logo/header-logo.png');
  }
  // GET FOOTER LOGO
  public function get_footer_logo()
  {
    return base_url('uploads/system/logo/footer-logo.png');
  }

  //GET ABOUT IMAGE
  public function get_about_image()
  {
    return base_url('uploads/images/about_us/about-us.jpg');
  }

  //GET SLIDER IMAGE
  public function get_slider_image($image)
  {
    return base_url('uploads/images/slider/' . $image);
  }

  public function remove_image($type = "", $photo = "")
  {
    $path = 'uploads/images/' . $type . '/' . $photo;
    if (file_exists($path)) {
      unlink($path);
    }
  }




  public function validateUploadedImage($file, $type)
  {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
      return get_phrase('upload_error');
    }

    $maxMB = $type === 'logo' ? 1 : 2;
    if ($file['size'] > $maxMB * 1024 * 1024) {
      return get_phrase('file_too_large') . " (max {$maxMB} Mo)";
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, ['image/jpeg', 'image/jpg', 'image/png'])) {
      return get_phrase('invalid_image_format');
    }

    list($width, $height) = getimagesize($file['tmp_name']);
    if (!$width || !$height) return get_phrase('corrupted_image');

    $ratio = $width / $height;
    $target = $type === 'logo' ? 1 : 16 / 9;
    if (abs($ratio - $target) / $target > 0.1) {
      $expected = $type === 'logo' ? '1:1' : '16:5';
      return get_phrase('invalid_image_ratio') . " {$expected} (actuel: {$width}×{$height})";
    }

    $minWidth = $type === 'logo' ? 400 : 1200;
    if ($width < $minWidth) {
      return get_phrase('image_too_small') . " (min {$minWidth}px)";
    }

    return true;
  }
  public function online_admission_school()
{
 
    $emailPattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';

    // Validate required fields
    if (
        $this->request->getPost('email') == '' ||
        !preg_match($emailPattern, $this->request->getPost('email')) ||
        $this->request->getPost('password') == '' ||
        $this->request->getPost('name') == '' ||
        $this->request->getPost('school_name') == '' ||
        $this->request->getPost('repeat-password') == ''
    ) {
        $audit_log_model = new \App\Models\Audit_log_model();
        $audit_log_model->log_failed_registration_school([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'school_name' => $this->request->getPost('school_name')
        ], 'validation_error');
      
        return json_encode([
            'status' => false,
            'message' => get_phrase('validation_error'),
            'csrf' => [
                'csrfName' => $this->security->getTokenName(),
                'csrfHash' => $this->security->getHash()
            ]
        ]);
    }

    // Validate password complexity
    $password = $this->request->getPost('password');
    $repeatPassword = $this->request->getPost('repeat-password');

    if ($password !== $repeatPassword) {
        return json_encode([
            'status' => false,
            'message' => get_phrase('passwords_do_not_match'),
            'csrf' => [
                'csrfName' => $this->security->getTokenName(),
                'csrfHash' => $this->security->getHash()
            ]
        ]);
    }

    if (strlen($password) < 8) {
        return json_encode([
            'status' => false,
            'message' => get_phrase('password_must_be_at_least_8_characters'),
            'csrf' => [
                'csrfName' => $this->security->getTokenName(),
                'csrfHash' => $this->security->getHash()
            ]
        ]);
    }

    if (!preg_match('/[A-Z]/', $password)) {
        return json_encode([
            'status' => false,
            'message' => get_phrase('password_must_contain_at_least_one_uppercase_letter'),
            'csrf' => [
                'csrfName' => $this->security->getTokenName(),
                'csrfHash' => $this->security->getHash()
            ]
        ]);
    }

    if (!preg_match('/[a-z]/', $password)) {
        return json_encode([
            'status' => false,
            'message' => get_phrase('password_must_contain_at_least_one_lowercase_letter'),
            'csrf' => [
                'csrfName' => $this->security->getTokenName(),
                'csrfHash' => $this->security->getHash()
            ]
        ]);
    }

    if (!preg_match('/[0-9]/', $password)) {
        return json_encode([
            'status' => false,
            'message' => get_phrase('password_must_contain_at_least_one_number'),
            'csrf' => [
                'csrfName' => $this->security->getTokenName(),
                'csrfHash' => $this->security->getHash()
            ]
        ]);
    }

    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        return json_encode([
            'status' => false,
            'message' => get_phrase('password_must_contain_at_least_one_special_character'),
            'csrf' => [
                'csrfName' => $this->security->getTokenName(),
                'csrfHash' => $this->security->getHash()
            ]
        ]);
    }

    // Check for duplicate school name and email
    $user_model = model('User_model');
    $school_duplication = $user_model->check_duplication_school('on_create', $this->request->getPost('school_name'));
    $email_duplication = $user_model->check_duplication('on_create', $this->request->getPost('email'));

    if (!$school_duplication && !$email_duplication) {
        $audit_log_model = new \App\Models\Audit_log_model();
        $audit_log_model->log_failed_registration_school([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'school_name' => $this->request->getPost('school_name')
        ], 'school_name_and_email_duplicate');
        
        return json_encode([
            'status' => false,
            'message' => get_phrase('this_school_name_and_email_already_exist'),
            'csrf' => [
                'csrfName' => $this->security->getTokenName(),
                'csrfHash' => $this->security->getHash()
            ]
        ]);
    }

    if (!$school_duplication) {
        $audit_log_model = new \App\Models\Audit_log_model();
        $audit_log_model->log_failed_registration_school([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'school_name' => $this->request->getPost('school_name')
        ], 'school_name_duplicate');
        
        return json_encode([
            'status' => false,
            'message' => get_phrase('this_school_name_already_exist'),
            'csrf' => [
                'csrfName' => $this->security->getTokenName(),
                'csrfHash' => $this->security->getHash()
            ]
        ]);
    }

    if (!$email_duplication) {
        $audit_log_model = new \App\Models\Audit_log_model();
        $audit_log_model->log_failed_registration_school([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'school_name' => $this->request->getPost('school_name')
        ], 'email_duplicate');
        
        return json_encode([
            'status' => false,
            'message' => get_phrase('this_email_already_exist'),
            'csrf' => [
                'csrfName' => $this->security->getTokenName(),
                'csrfHash' => $this->security->getHash()
            ]
        ]);
    }

    // Prepare school data
    // Price step removed, defaulting to access=1 (public/free) unless visibility is set to private
    if(htmlspecialchars($this->request->getPost('i_am')) == 'Particulier'){
      $access = 1;
    } else {
      $access = $this->request->getPost('visibility') ? 1 : 0;
    }
  
    
    // Normaliser le country code depuis le formulaire Tax_residence
    $tax_residence_input = htmlspecialchars($this->request->getPost('Tax_residence'));
    $country_code = null;
    if ($tax_residence_input === 'MA') {
        $country_code = 'MA';
    } elseif ($tax_residence_input === 'UAE' || $tax_residence_input === 'AE') {
        $country_code = 'AE';
    } elseif (!empty($tax_residence_input)) {
        $country_code = strtoupper(substr($tax_residence_input, 0, 2));
    }

    $school_data = array_merge([
        'name' => html_entity_decode(htmlspecialchars($this->request->getPost('school_name'))),
        'country' => $country_code, // Code pays (MA, AE, etc.)
        'Rue' => htmlspecialchars($this->request->getPost('street')),
        'Numero' => htmlspecialchars($this->request->getPost('number')),
        'Ville' => htmlspecialchars($this->request->getPost('city')),
        'Codepostal' => htmlspecialchars($this->request->getPost('postal_code')),
        'phone' => htmlspecialchars($this->request->getPost('school_phone')),
        'status' => 0, // School pending approval
        'description' => htmlspecialchars($this->request->getPost('school_description')),
        'access' => $access,
        'category' => htmlspecialchars($this->request->getPost('category')),
        // 'price' => 0, // Price step removed (Managed by DB default NULL)
    ], community_subscription_seed());

    // Insert school
    \db()->table('schools')->insert($school_data);
    $school_id = \db()->insertID();

    // Insert payment settings
    $payment_settings = [
        [
            'key' => 'stripe_settings',
            'value' => '[{\"stripe_active\":\"yes\",\"stripe_mode\":\"on\",\"stripe_test_secret_key\":\"1234\",\"stripe_test_public_key\":\"1234\",\"stripe_live_secret_key\":\"1234\",\"stripe_live_public_key\":\"1234\",\"stripe_currency\":\"USD\"}]',
            'school_id' => $school_id
        ],
        [
            'key' => 'paypal_settings',
            'value' => '[{\"paypal_active\":\"yes\",\"paypal_mode\":\"sandbox\",\"paypal_client_id_sandbox\":\"1234\",\"paypal_client_id_production\":\"1234\",\"paypal_currency\":\"USD\"}]',
            'school_id' => $school_id
        ]
    ];
    \db()->table('payment_settings')->insertBatch($payment_settings);
    $rate = ($country_code === 'MA') ? 20 : 5;
    
    // Insert school settings
    $settings_school = [
        'school_id' => $school_id,
        'system_currency' => htmlspecialchars($this->request->getPost('currency')),
        'currency_position' => 'left',
        'language' => 'english',
        'type' => htmlspecialchars($this->request->getPost('i_am')),
        'vat_enabled' => 1, // TVA activée par défaut ou selon besoin
        'vat_rate' => $rate
    ];
    \db()->table('settings_school')->insert($settings_school);

    // Generate email verification token
    $verification_token = bin2hex(random_bytes(32));
    $verification_expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

    // Prepare user (admin/mentor) data
    $plainPassword = $this->request->getPost('password'); // <- mot de passe en clair
    $admin_data = [
        'name' => htmlspecialchars($this->request->getPost('name') ?? ''),
        'email' => htmlspecialchars($this->request->getPost('email') ?? ''),
        'gender' => htmlspecialchars($this->request->getPost('gender') ?? ''),
        'phone' => htmlspecialchars($this->request->getPost('phone') ?? ''),
        'language' => htmlspecialchars($this->request->getPost('communityLang') ?? ''),
        'password' => password_hash($plainPassword, PASSWORD_DEFAULT),
        'role' => 'admin',
        'school_id' => $school_id,
        'status' => 3, // Pending status - requires email verification
        'watch_history' => '[]',
        'email_verification_token' => $verification_token,
        'email_verification_expires' => $verification_expires
    ];

//     var_dump($this->request->getPost('name')); 
// // ou
// echo "Name reçu : " . $this->request->getPost('name');
// exit; // arrêter l'exécution pour voir le résultat

    // Insert user
    \db()->table('users')->insert($admin_data);
    $user_id = \db()->insertID();
    \db()->table('user_schools')->insert([
      'user_id'   => $user_id,
      'school_id' => $school_id,
      'role'      => 'admin'
    ]);
    

    
    // Handle school image upload (logo) - avec compression
    if ($this->isUploadOk('school_image')) {
      $validation = $this->validateImageUpload('school_image');
      if (!$validation['valid']) {
        return json_encode([
          'status' => false,
          'error_type' => 'logo',
          'message' => $validation['message'],
          'csrf' => [
            'csrfName' => $this->security->getTokenName(),
            'csrfHash' => $this->security->getHash()
          ]
        ]);
      }

      $schoolImage = $this->uploadedFile('school_image');
      $logo_dir = 'uploads/schools/';
      if (!is_dir($logo_dir)) {
          mkdir($logo_dir, 0755, true);
      }
      
      // Compresser et sauvegarder le logo (512x512 recommandé, qualité 90%)
      $logo_result = $this->compress_and_save_image(
          $schoolImage->getTempName(),
          $logo_dir . $school_id,
          512,   // max width
          512,   // max height
          90     // qualité JPEG
      );
      
      if ($logo_result === false) {
          log_message('error', 'Failed to compress school logo for school_id: ' . $school_id);
          return json_encode([
              'status' => false,
              'error_type' => 'logo',
              'message' => get_phrase('image_processing_failed_please_try_another_image'),
              'csrf' => [
                  'csrfName' => $this->security->getTokenName(),
                  'csrfHash' => $this->security->getHash()
              ]
          ]);
      }
    }

    // Handle school cover upload - avec compression
    if ($this->isUploadOk('communityCover')) {
      $validation = $this->validateImageUpload('communityCover');
      if (!$validation['valid']) {
        return json_encode([
          'status' => false,
          'error_type' => 'cover',
          'message' => $validation['message'],
          'csrf' => [
            'csrfName' => $this->security->getTokenName(),
            'csrfHash' => $this->security->getHash()
          ]
        ]);
      }

      $communityCover = $this->uploadedFile('communityCover');
      $cover_dir = 'uploads/communityCover/';
      if (!is_dir($cover_dir)) {
          mkdir($cover_dir, 0755, true);
      }
      
      // Compresser et sauvegarder la cover (1920x600 recommandé, qualité 90%)
      $cover_result = $this->compress_and_save_image(
          $communityCover->getTempName(),
          $cover_dir . $school_id,
          1920,  // max width
          600,   // max height
          90     // qualité JPEG
      );
      
      if ($cover_result === false) {
          log_message('error', 'Failed to compress school cover for school_id: ' . $school_id);
          return json_encode([
              'status' => false,
              'error_type' => 'cover',
              'message' => get_phrase('cover_image_processing_failed_please_try_another_image'),
              'csrf' => [
                  'csrfName' => $this->security->getTokenName(),
                  'csrfHash' => $this->security->getHash()
              ]
          ]);
      }
    }

    // Send confirmation emails
    $email_model = model('Email_model');
    $email_model->School_online_admission($admin_data['email'], $school_data['name'], $admin_data['name']);
    $email_model->School_online_admission_superadmin($admin_data['email'], $school_data['name'], $admin_data['name']);
    
    // Send email verification
    try {
        $email_sent = $email_model->send_email_verification($admin_data['email'], $verification_token, $admin_data['name']);
        if (!$email_sent) {
            log_message('error', 'Failed to send email verification to: ' . $admin_data['email']);
        }
    } catch (\Exception $e) {
        log_message('error', 'Email verification error: ' . $e->getMessage());
    }

    // Log admission in audit trail
    $audit_log_model = new \App\Models\Audit_log_model();
    $audit_log_model->log_online_admission_school(
        $user_id,
        $school_id,
        [
            'name' => $school_data['name'],
            'email' => $admin_data['email'],
            'country' => $school_data['country'],
            'city' => $school_data['Ville'],
            'category' => $school_data['category'],
            'type' => $settings_school['type'],
            'phone' => $school_data['phone']
        ]
    );

    // Success response
    return json_encode([
        'status' => true,
        'message' => get_phrase('Votre inscription a été effectuée avec succès.'),
        'csrf' => [
            'csrfName' => $this->security->getTokenName(),
            'csrfHash' => $this->security->getHash()
        ]
    ]);
}

  function contains($table_name = '', $column_name = '', $value = '')
  {
    $query = \db()->table($table_name)->where($column_name, $value)->get()->numRows();
    return $query > 0;
  }

  function get_categories()
  {
    return \db()->table('categories')
      ->orderBy('name', 'ASC')
      ->get()
      ->getResultArray();
  }

  function get_category_formated($category)
  {
    $cat_formated = str_replace(" ", "_", $category);
    return $cat_formated;
  }


  function get_school_courses($school_id)
  {
      return \db()->table('course')
        ->select('course.*, classes.price')
        ->join('classes', 'classes.id = course.class_id', 'left')
        ->where('course.school_id', $school_id)
        ->get()
        ->getResultArray();
  }

  public function get_course_image($thumbnail)
  {
    if (file_exists('uploads/course_thumbnail/' . $thumbnail))

      echo base_url() . 'uploads/course_thumbnail/' . $thumbnail;
    else
      echo base_url() . 'uploads/course_thumbnail/placeholder.png';
  }

  public function update_user_language($user_id, $lang_name) {
    return \db()->table('users')
      ->where('id', $user_id)
      ->update(['language' => $lang_name]);
  }

  public function count_users_by_role($role, $school_id) {
    return \db()->table('users')->where('school_id', $school_id)->where('role', $role)->get()->getResultArray();
  }

  public function count_frontend_events($school_id) {
    return \db()->table('frontend_events')->where('school_id', $school_id)->get()->getResultArray();
  }

  public function count_frontend_gallery($school_id) {
    return \db()->table('frontend_gallery')->where('school_id', $school_id)->get()->getResultArray();
  }

  public function count_noticeboard($school_id, $session) {
    return \db()->table('noticeboard')->where('school_id', $school_id)->get()->getResultArray();
  }

  public function get_student_by_user_and_school($user_id, $school_id) {
    return \db()->table('students')->where('student_id', $user_id)->where('school_id', $school_id)->get()->getRowArray();
  }

  public function get_settings_school($school_id) {
    return \db()->table('settings_school')->where('school_id', $school_id)->get()->getRowArray();
  }

  public function get_student_status($user_id, $school_id) {
    return \db()->table('students')->where('student_id', $user_id)->where('school_id', $school_id)->get()->getRow('status');
  }

  public function get_school_by_category($category) {
    return \db()->table('schools')->where('category', $category)->get()->getResultArray();
  }

  public function count_all_schools() {
    return \db()->table('schools')->countAllResults();
  }

  public function get_user_schools_with_role($user_id) {
    return \db()->table('user_schools us')
      ->select('us.school_id, s.name as community_name, us.role')
      ->join('schools s', 's.id = us.school_id', 'inner')
      ->where('us.user_id', $user_id)
      ->where('us.school_id IS NOT NULL', null, false)
      ->where('s.id IS NOT NULL', null, false)
      ->get()
      ->getResultArray();
  }

  public function get_student_entry_by_user($user_id) {
    return \db()->table('students')->where('user_id', $user_id)->get()->getRowArray();
  }

  public function get_user_role_in_school($user_id, $school_id) {
    return \db()->table('user_schools')
      ->where('user_id', $user_id)
      ->where('school_id', $school_id)
      ->get()
      ->getRow();
  }

  public function get_student_communities($user_id) {
    return \db()->table('user_schools us')
      ->select('us.school_id, s.name as community_name')
      ->join('schools s', 's.id = us.school_id', 'inner')
      ->join('students st', 'st.school_id = us.school_id AND st.user_id = us.user_id', 'inner')
      ->where('us.user_id', $user_id)
      ->where('us.role', 'student')
      ->where('us.school_id IS NOT NULL', null, false)
      ->where('st.status', 1)
      ->get()
      ->getResultArray();
  }

  public function update_user_role($user_id, $data) {
    return \db()->table('users')->where('id', $user_id)->update($data);
  }

  public function count_gallery_images($gallery_id) {
    return \db()->table('frontend_gallery_image')->where('frontend_gallery_id', $gallery_id)->get()->getResultArray();
  }

  public function count_alumni_gallery_photos($gallery_id) {
    return \db()->table('alumni_gallery_photos')->where('alumni_gallery_id', $gallery_id)->get()->getResultArray();
  }

  public function insert_school($data) {
    \db()->table('schools')->insert($data);
    return \db()->insertID();
  }

  public function insert_user_school($data) {
    return \db()->table('user_schools')->insert($data);
  }

  public function insert_payment_settings_batch($data) {
    return \db()->table('payment_settings')->insertBatch($data);
  }

  public function insert_settings_school($data) {
    return \db()->table('settings_school')->insert($data);
  }

  public function get_user_roles($user_id) {
    return \db()->table('user_schools us')
      ->select('us.role')
      ->where('us.user_id', $user_id)
      ->groupBy('us.role')
      ->get()
      ->getResultArray();
  }

  public function get_user_communities_with_status($user_id, $role) {
    return \db()->table('user_schools us')
      ->select('s.id as school_id, s.name as community_name, st.status')
      ->join('schools s', 's.id = us.school_id')
      ->join('students st', 'st.school_id = us.school_id AND st.user_id = us.user_id', 'inner')
      ->where('us.user_id', $user_id)
      ->where('us.role', $role)
      ->get()
      ->getResultArray();
  }

  public function get_user_communities($user_id, $role) {
    return \db()->table('user_schools us')
      ->select('s.id as school_id, s.name as community_name')
      ->join('schools s', 's.id = us.school_id')
      ->where('us.user_id', $user_id)
      ->where('us.role', $role)
      ->get()
      ->getResultArray();
  }

  public function get_all_user_communities_with_status($user_id, $role) {
    return \db()->table('user_schools us')
      ->select('s.id as school_id, s.name as community_name, us.role, st.status')
      ->join('schools s', 's.id = us.school_id')
      ->join('students st', 'st.school_id = us.school_id AND st.user_id = us.user_id', 'inner')
      ->where('us.user_id', $user_id)
      ->where('us.role', ucfirst($role))
      ->get()
      ->getResultArray();
  }

  public function get_all_user_communities($user_id, $role) {
    return \db()->table('user_schools us')
      ->select('s.id as school_id, s.name as community_name, us.role')
      ->join('schools s', 's.id = us.school_id')
      ->where('us.user_id', $user_id)
      ->where('us.role', ucfirst($role))
      ->get()
      ->getResultArray();
  }

}




