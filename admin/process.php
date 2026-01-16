<?php
session_start();

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit;
}

// Category to class mapping
$category_classes = [
    'Personal Finance' => 'personal-finance',
    'Investing Basics' => 'investing-basics',
    'Market Indices' => 'market-indices',
    'Tax' => 'tax'
];

function slugify($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^\w\s-]/', '', $text);
    $text = preg_replace('/[-\s]+/', '-', $text);
    return trim($text, '-');
}

function formatContentToHtml($content) {
    $lines = explode("\n", $content);
    $html = '';
    $in_list = false;
    $list_type = '';
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Empty line
        if (empty($line)) {
            if ($in_list) {
                $html .= "            </{$list_type}>\n\n";
                $in_list = false;
            }
            continue;
        }
        
        // H3 heading
        if (preg_match('/^###\s+(.+)$/', $line, $matches)) {
            if ($in_list) {
                $html .= "            </{$list_type}>\n\n";
                $in_list = false;
            }
            $html .= "            <h3>" . trim($matches[1]) . "</h3>\n\n";
            continue;
        }
        
        // H2 heading
        if (preg_match('/^##\s+(.+)$/', $line, $matches)) {
            if ($in_list) {
                $html .= "            </{$list_type}>\n\n";
                $in_list = false;
            }
            $html .= "            <h2>" . trim($matches[1]) . "</h2>\n\n";
            continue;
        }
        
        // Bullet list item
        if (preg_match('/^[-*]\s+(.+)$/', $line, $matches)) {
            if (!$in_list) {
                $html .= "            <ul>\n";
                $in_list = true;
                $list_type = 'ul';
            } elseif ($list_type !== 'ul') {
                $html .= "            </{$list_type}>\n";
                $html .= "            <ul>\n";
                $list_type = 'ul';
            }
            $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $matches[1]);
            $html .= "                <li>" . $text . "</li>\n";
            continue;
        }
        
        // Numbered list item
        if (preg_match('/^\d+\.\s+(.+)$/', $line, $matches)) {
            if (!$in_list) {
                $html .= "            <ol>\n";
                $in_list = true;
                $list_type = 'ol';
            } elseif ($list_type !== 'ol') {
                $html .= "            </{$list_type}>\n";
                $html .= "            <ol>\n";
                $list_type = 'ol';
            }
            $html .= "                <li>" . trim($matches[1]) . "</li>\n";
            continue;
        }
        
        // Regular paragraph
        if ($in_list) {
            $html .= "            </{$list_type}>\n\n";
            $in_list = false;
        }
        $line = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $line);
        $html .= "            <p>" . $line . "</p>\n\n";
    }
    
    // Close any open list
    if ($in_list) {
        $html .= "            </{$list_type}>\n\n";
    }
    
    return $html;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = $_POST['title'];
    $category = $_POST['category'];
    $reading_time = $_POST['reading_time'];
    $description = $_POST['description'];
    $content = $_POST['content'];
    
    $slug = slugify($title);
    $date = date('F d, Y');
    
    // Create blog folder
    $blog_folder = '../posts/' . $slug;
    if (!is_dir($blog_folder)) {
        mkdir($blog_folder, 0755, true);
    }
    
    // Handle thumbnail upload - save as thumbnail.png in blog folder
    $thumbnail_saved = false;
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
        $thumbnail_path = $blog_folder . '/thumbnail.png';
        
        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnail_path)) {
            $thumbnail_saved = true;
        } else {
            die("Error uploading thumbnail");
        }
    }
    
    // Format content
    $html_content = formatContentToHtml($content);
    
    // Create blog post HTML
    $blog_html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title} - Wooli</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bronze: #B8860B;
            --bronze-light: #D4AF37;
            --bronze-dark: #8B6914;
            --dark: #1a1a1a;
            --light: #f5f5f0;
            --white: #ffffff;
            --gray: #666;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.8;
            color: var(--dark);
            background: var(--light);
        }

        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 1rem 3%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
        }

        .logo-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        .logo-text {
            font-size: 1.6rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--bronze) 0%, var(--bronze-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: 3px;
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--dark);
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
        }

        article {
            max-width: 800px;
            margin: 10rem auto 5rem;
            padding: 0 5%;
        }

        .article-header {
            margin-bottom: 3rem;
        }

        .category-tag {
            display: inline-block;
            padding: 0.6rem 1.5rem;
            background: var(--bronze);
            color: white;
            border-radius: 25px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        h1 {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: var(--dark);
            letter-spacing: -1px;
        }

        .article-meta {
            display: flex;
            gap: 2rem;
            color: var(--gray);
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .article-meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .article-description {
            font-size: 1.3rem;
            color: var(--gray);
            line-height: 1.7;
            margin-bottom: 3rem;
            padding-bottom: 3rem;
            border-bottom: 2px solid var(--bronze);
        }

        .article-content {
            font-size: 1.15rem;
            line-height: 1.9;
        }

        .article-content h2 {
            font-size: 2.2rem;
            font-weight: 800;
            margin: 3rem 0 1.5rem;
            color: var(--bronze);
        }

        .article-content h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 2.5rem 0 1.2rem;
            color: var(--dark);
        }

        .article-content p {
            margin-bottom: 1.5rem;
        }

        .article-content ul,
        .article-content ol {
            margin: 1.5rem 0;
            padding-left: 2rem;
        }

        .article-content li {
            margin-bottom: 0.8rem;
        }

        .article-content strong {
            color: var(--bronze);
            font-weight: 700;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            padding: 1rem 2rem;
            background: var(--bronze);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 700;
            margin: 4rem 0 2rem;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            transform: translateX(-5px);
            box-shadow: 0 8px 20px rgba(184, 134, 11, 0.3);
        }

        footer {
            background: linear-gradient(135deg, var(--dark) 0%, #0a0a0a 100%);
            color: white;
            padding: 4rem 5% 2rem;
            text-align: center;
        }

        .footer-text {
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--bronze);
            margin-bottom: 1.5rem;
        }

        .copyright {
            opacity: 0.7;
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2.2rem;
            }

            article {
                margin-top: 8rem;
            }

            .nav-links {
                display: none;
            }

            .logo-img {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-container">
            <a href="../../index.html" class="logo-container">
                <img src="../../logo.png" alt="Wooli Logo" class="logo-img">
                <span class="logo-text">WOOLI</span>
            </a>
            <ul class="nav-links">
                <li><a href="../../index.html">Home</a></li>
                <li><a href="../../blog.html">Blog</a></li>
                <li><a href="../../about.html">About</a></li>
                <li><a href="../../contact.html">Contact</a></li>
            </ul>
        </div>
    </nav>

    <article>
        <div class="article-header">
            <span class="category-tag">{$category}</span>
            <h1>{$title}</h1>
            <div class="article-meta">
                <span>📅 {$date}</span>
                <span>⏱ {$reading_time}</span>
            </div>
            <p class="article-description">{$description}</p>
        </div>

        <div class="article-content">
{$html_content}
        </div>

        <a href="../../blog.html" class="back-button">← Back to Blog</a>
    </article>

    <footer>
        <div class="footer-text">WOOLI</div>
        <p class="copyright">&copy; 2024 Wooli. Empowering financial growth across India.</p>
    </footer>
</body>
</html>
HTML;

    // Save blog post as index.html in folder
    $blog_file = $blog_folder . '/index.html';
    file_put_contents($blog_file, $blog_html);
    
    // Create blog card for blog.html
    $category_class = $category_classes[$category];
    $blog_card = <<<CARD

                <!-- BLOG POST -->
                <a href="posts/{$slug}/" class="blog-card" data-category="{$category_class}">
                    <div class="blog-card-image">
                        <img src="posts/{$slug}/thumbnail.png" alt="{$title}">
                    </div>
                    <div class="blog-card-body">
                        <div class="blog-meta">
                            <span class="blog-category">{$category}</span>
                            <span class="reading-time">⏱ {$reading_time}</span>
                        </div>
                        <h3>{$title}</h3>
                        <p>{$description}</p>
                        <span class="read-more">Read Full Article</span>
                    </div>
                </a>

CARD;

    // Update blog.html
    $blog_html_content = file_get_contents('../blog.html');
    $marker = '<!-- ADD NEW BLOG POSTS BELOW THIS LINE -->';
    $blog_html_content = str_replace($marker, $marker . $blog_card, $blog_html_content);
    file_put_contents('../blog.html', $blog_html_content);
    
    // Redirect back with success
    header('Location: dashboard.php?success=1');
    exit;
}
?>
