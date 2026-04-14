<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Articles extends BaseController {

    protected $theme;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        $this->Article_model = model('Article_model');
        $this->settings_model = model('Settings_model');
        $this->frontend_model = model('Frontend_model');
        $this->user_model = model('User_model');
        $this->theme = get_frontend_settings('theme');
    }

    public function index() {
        $page_data['page_name'] = 'Articles/index';
        $page_data['page_title'] = get_phrase('trends');
        $page_data['articles'] = $this->Article_model->get_articles();
        return view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function show($slug) {
        $article = $this->Article_model->get_article_by_slug($slug);
        if (!$article) {
            show_404();
        }
        $page_data['article'] = $article;
        $page_data['page_name'] = 'Articles/show';
        $page_data['page_title'] = $article['title'];
        return view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function category($slug) {
        $page_data['articles'] = $this->Article_model->get_articles_by_category($slug);
        $page_data['selected_category'] = $slug;
        $page_data['page_name'] = 'Articles/category';
        $page_data['page_title'] = get_phrase('category') . ': ' . $slug;
        return view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function tag($slug) {
        $page_data['articles'] = $this->Article_model->get_articles_by_tag($slug);
        $page_data['page_name'] = 'Articles/tag';
        $page_data['page_title'] = get_phrase('tag') . ': ' . $slug;
        return view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function search() {
        $query = $this->request->getGet('q');
        $page_data['articles'] = $this->Article_model->search_articles($query);
        $page_data['page_name'] = 'Articles/index';
        $page_data['page_title'] = get_phrase('search_results') . ': ' . esc($query);
        return view('frontend/' . $this->theme . '/index', $page_data);
    }

    public function page($page) {
        $allowed_pages = ['about_blog', 'faq', 'contact'];
        if (!in_array($page, $allowed_pages)) {
            show_404();
        }
        $page_data['page_name'] = 'Articles/pages/' . $page;
        $page_data['page_title'] = get_phrase($page);
        return view('frontend/' . $this->theme . '/index', $page_data);
    }
}
