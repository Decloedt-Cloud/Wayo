<?php

namespace App\Models;

use CodeIgniter\Model;

class Article_model extends Model {
    protected $table            = 'articles';
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

    public function __construct() {
        parent::__construct();
    }

    public function get_articles($limit = null, $offset = 0) {
        if (!\db()->tableExists('articles')) {
            return $this->get_mock_data();
        }
        $builder = \db()->table('articles');
        $builder->orderBy('created_at', 'DESC');
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        return $builder->get()->getResultArray();
    }

    public function get_article_by_slug($slug) {
        if (!\db()->tableExists('articles')) {
            $mock = $this->get_mock_data();
            foreach ($mock as $article) {
                if ($article['slug'] == $slug) return $article;
            }
            return null;
        }
        return \db()->table('articles')->get()->getRowArray();
    }

    public function get_articles_by_category($category_slug) {
        if (!\db()->tableExists('articles')) {
            return $this->get_mock_data();
        }
        return \db()->table('articles')->get()->getResultArray();
    }

    public function get_articles_by_tag($tag_slug) {
        if (!\db()->tableExists('articles')) {
            return $this->get_mock_data();
        }
        $builder = \db()->table('articles');
        $builder->like('tags', $tag_slug);
        return $builder->get()->getResultArray();
    }

    public function search_articles($query) {
        if (!\db()->tableExists('articles')) {
            $mock = $this->get_mock_data();
            return array_filter($mock, function($article) use ($query) {
                return stripos($article['title'], $query) !== false || 
                       stripos($article['summary'], $query) !== false;
            });
        }
        $builder = \db()->table('articles');
        $builder->groupStart()
                ->like('title', $query)
                ->orLike('summary', $query)
                ->orLike('content', $query)
                ->groupEnd();
        return $builder->get()->getResultArray();
    }

    private function get_mock_data() {
        return [
            [
            'id' => 1,
            'title' => get_phrase('How ClawdBot Is Changing Digital Content'),
            'slug' => 'how-clawd-bot-is-changing-digital-content',
            'summary' => get_phrase('Artificial intelligence is redefining how digital content is produced, structured, and distributed. Discover how AI-assisted tools like ClawdBot are transforming content creation into structured knowledge.'),

            'content' => '
                <h2>'.get_phrase('Introduction').'</h2>
                <p>'.get_phrase('Artificial intelligence is redefining how digital content is produced, structured, and distributed. Tools such as ClawdBot illustrate a growing shift toward AI-assisted creation, enabling users to generate content faster and more efficiently. Beyond simple automation, this evolution raises a broader question: how can AI-generated content be transformed into structured knowledge, learning experiences, and long-term value?').'</p>

                <h2>'.get_phrase('ClawdBot as a Content Creation Tool').'</h2>
                <p>'.get_phrase('ClawdBot is part of a new generation of AI tools designed to simplify content production. By assisting users in drafting, organizing, and refining content, ClawdBot helps reduce the friction traditionally associated with writing and ideation. Such tools are increasingly used by:').'</p>

                <ul>
                    <li>'.get_phrase('Content creators and marketers').'</li>
                    <li>'.get_phrase('Educators and trainers').'</li>
                    <li>'.get_phrase('Entrepreneurs building digital products').'</li>
                </ul>

                <p>'.get_phrase('The appeal lies in speed and accessibility. Users no longer need advanced technical skills to benefit from AI; instead, they can focus on ideas, expertise, and messaging while the tool supports execution.').'</p>

                <h2>'.get_phrase('From Generated Content to Structured Knowledge').'</h2>
                <p>'.get_phrase('While tools like ClawdBot excel at generating content, content alone is rarely the final objective. Creators today aim to build:').'</p>

                <ul>
                    <li>'.get_phrase('Online courses').'</li>
                    <li>'.get_phrase('Educational programs').'</li>
                    <li>'.get_phrase('Private learning communities').'</li>
                    <li>'.get_phrase('Structured knowledge products').'</li>
                </ul>

                <p>'.get_phrase('This shift highlights a key trend: AI-generated content becomes truly valuable when it is organized, contextualized, and delivered through a coherent learning experience.').'</p>

                <h2>'.get_phrase('How Wayo Extends the Value of AI-Generated Content').'</h2>
                <p>'.get_phrase('This is where Wayo positions itself differently. Wayo integrates AI capabilities similar to those used in tools like ClawdBot, but applies them directly to course creation and community management.').'</p>

                <p>'.get_phrase('Within Wayo, creators can:').'</p>
                <ul>
                    <li>'.get_phrase('Transform ideas or raw content into structured courses').'</li>
                    <li>'.get_phrase('Generate lessons and modules with AI assistance').'</li>
                    <li>'.get_phrase('Build and manage a community around their expertise').'</li>
                    <li>'.get_phrase('Centralize content, learners, and interactions in one platform').'</li>
                </ul>

                <p>'.get_phrase('The result is a workflow that goes beyond content generation, focusing instead on knowledge delivery and engagement.').'</p>

                <h2>'.get_phrase('A Shift Toward Integrated Creator Platforms').'</h2>
                <p>'.get_phrase('The broader trend is clear: creators are moving away from isolated tools toward integrated platforms. Rather than using one tool to generate content and another to distribute or monetize it, they seek solutions that combine content creation, learning, and community.').'</p>

                <p>'.get_phrase('In this context, ClawdBot represents the starting point of the process—efficient content creation—while platforms like Wayo represent the next step: turning that content into sustainable educational projects.').'</p>

                <h3>'.get_phrase('Conclusion').'</h3>

                <p>'.get_phrase('ClawdBot reflects how AI is accelerating content creation and making it accessible to a wider audience. However, the future lies not only in producing content faster, but in transforming that content into structured learning experiences and engaged communities. By combining AI-assisted creation with course building and community management, platforms like Wayo illustrate how creators can move from content generation to long-term value creation.').'</p>',
                'image' => base_url('uploads/images/trends/optimized/clawdBot.webp'),
                'category' => 'Technology',
                'category_slug' => get_phrase('technology'),
                'author' => get_phrase('Wayo Team'),
                'created_at' => date('Y-m-d H:i:s'),
                'tags' => 'AI, ClawdBot, content creation, education technology',
                'meta_title' => 'ClawdBot and the Evolution of Content Creation',
                'meta_description' => 'Discover how ClawdBot is changing content creation and how platforms like Wayo turn AI-generated content into structured learning and communities.'
            ]
            // ,
            // [
            //     'id' => 2,
            //     'title' => 'Comment créer une communauté engagée',
            //     'slug' => 'comment-creer-une-communaute-engagee',
            //     'summary' => 'Les secrets pour fédérer vos membres et dynamiser vos échanges.',
            //     'content' => 'Le contenu détaillé de l\'article sur les communautés...',
            //     'image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80',
            //     'category' => 'Conseils',
            //     'category_slug' => 'conseils',
            //     'author' => 'Jean Dupont',
            //     'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            //     'tags' => 'community, marketing'
            // ]
        ];
    }
}
