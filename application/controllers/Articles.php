<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Articles extends CI_Controller {

    protected $theme;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->model('Article_model');
        $this->load->model('Settings_model', 'settings_model');
        $this->load->model('Frontend_model', 'frontend_model');
        $this->load->model('User_model', 'user_model');
        $this->theme = get_frontend_settings('theme');
    }

    public function index() {
        $page_data['page_name'] = 'Articles/index';
        $page_data['page_title'] = get_phrase('trends');
        $page_data['articles'] = $this->Article_model->get_articles();
        $this->load->view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function show($slug) {
        $article = $this->Article_model->get_article_by_slug($slug);
        if (!$article) {
            show_404();
        }
        $page_data['article'] = $article;
        $page_data['page_name'] = 'Articles/show';
        $page_data['page_title'] = $article['title'];
        $this->load->view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function category($slug) {
        $page_data['articles'] = $this->Article_model->get_articles_by_category($slug);
        $page_data['selected_category'] = $slug;
        $page_data['page_name'] = 'Articles/category';
        $page_data['page_title'] = get_phrase('category') . ': ' . $slug;
        $this->load->view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function tag($slug) {
        $page_data['articles'] = $this->Article_model->get_articles_by_tag($slug);
        $page_data['page_name'] = 'Articles/tag';
        $page_data['page_title'] = get_phrase('tag') . ': ' . $slug;
        $this->load->view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function page($page) {
        // Validation basique pour éviter le directory traversal
        $allowed_pages = ['about_blog', 'faq', 'contact'];
        if (!in_array($page, $allowed_pages)) {
            show_404();
        }
        $page_data['page_name'] = 'Articles/pages/' . $page;
        $page_data['page_title'] = get_phrase($page);
        $this->load->view('frontend/' . $this->theme . '/index', $page_data);
    }
}
