<?php
session_start();

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit;
}

$success_message = '';
if (isset($_GET['success'])) {
    $success_message = "✅ Blog post created successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog Post - Wooli Admin</title>
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
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--light);
            padding: 2rem;
        }

        .header {
            max-width: 1000px;
            margin: 0 auto 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            font-size: 2rem;
            background: linear-gradient(135deg, var(--bronze) 0%, var(--bronze-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logout-btn {
            padding: 0.8rem 1.5rem;
            background: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 2rem;
            color: var(--bronze);
            margin-bottom: 2rem;
        }

        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
        }

        input[type="text"],
        input[type="file"],
        select,
        textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid rgba(184, 134, 11, 0.3);
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        textarea {
            min-height: 400px;
            resize: vertical;
            font-family: 'Courier New', monospace;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--bronze);
            box-shadow: 0 0 0 4px rgba(184, 134, 11, 0.1);
        }

        .help-text {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.5rem;
        }

        .btn {
            padding: 1.2rem 3rem;
            background: linear-gradient(135deg, var(--bronze) 0%, var(--bronze-dark) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(184, 134, 11, 0.3);
        }

        .formatting-guide {
            background: var(--light);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .formatting-guide h3 {
            color: var(--bronze);
            margin-bottom: 1rem;
        }

        .formatting-guide code {
            background: white;
            padding: 0.2rem 0.5rem;
            border-radius: 5px;
            font-family: monospace;
        }

        .note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <h1>WOOLI Admin</h1>
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="container">
        <h2>Create New Blog Post</h2>

        <?php if ($success_message): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="note">
            <strong>📝 Note:</strong> Upload your .docx file to Claude first. Claude will format it properly, then you paste the formatted content below.
        </div>

        <form action="process.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Blog Title *</label>
                <input type="text" name="title" required>
            </div>

            <div class="form-group">
                <label>Category *</label>
                <select name="category" required>
                    <option value="Personal Finance">Personal Finance</option>
                    <option value="Investing Basics">Investing Basics</option>
                    <option value="Market Indices">Market Indices</option>
                    <option value="Tax">Tax</option>
                </select>
            </div>

            <div class="form-group">
                <label>Reading Time *</label>
                <input type="text" name="reading_time" placeholder="e.g., 5 min read" required>
            </div>

            <div class="form-group">
                <label>Thumbnail Image *</label>
                <input type="file" name="thumbnail" accept="image/png,image/jpeg,image/jpg" required>
                <p class="help-text">Upload .png or .jpg image</p>
            </div>

            <div class="form-group">
                <label>Short Description *</label>
                <textarea name="description" rows="3" required></textarea>
                <p class="help-text">2-3 lines for the blog card preview</p>
            </div>

            <div class="formatting-guide">
                <h3>Formatting Reference (Claude does this for you)</h3>
                <p><strong>Headings:</strong> <code>## Heading 2</code> or <code>### Heading 3</code></p>
                <p><strong>Bold:</strong> <code>**bold text**</code></p>
                <p><strong>Lists:</strong> <code>- item</code> or <code>1. item</code></p>
                <p><strong>Paragraphs:</strong> Separate with blank lines</p>
            </div>

            <div class="form-group">
                <label>Blog Content * (Paste Claude's formatted text here)</label>
                <textarea name="content" required></textarea>
            </div>

            <button type="submit" class="btn">Publish Blog Post</button>
        </form>
    </div>
</body>
</html>
