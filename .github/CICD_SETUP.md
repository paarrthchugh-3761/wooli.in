# CI/CD Pipeline Setup for Wooli.in

## 🔄 Automated Deployment Workflow

### How It Works

```
GitHub Repository
    │
    ├── Push to 'staging' branch
    │   └── Auto-deploys to GitHub Pages
    │       • URL: https://paarrthchugh-3761.github.io/wooli.in
    │       • No admin panel (static files only)
    │       • Perfect for testing changes
    │
    └── Push to 'main' branch
        └── Auto-deploys to Hostinger
            • URL: https://wooli.in
            • Includes admin panel (PHP works)
            • Full production site

```

---

## ⚙️ Setup Instructions

### Step 1: Enable GitHub Pages

1. Go to your GitHub repository: `https://github.com/paarrthchugh-3761/wooli.in`
2. Click **Settings** → **Pages**
3. Under "Build and deployment":
   - Source: **GitHub Actions** (not "Deploy from branch")
4. Save

### Step 2: Add Hostinger FTP Credentials (GitHub Secrets)

1. **Get your Hostinger FTP credentials:**
   - Log into Hostinger
   - Go to **Hosting** → **File Manager**
   - Look for **FTP Accounts** or **FTP Access**
   - Note down:
     - FTP Server (e.g., `ftp.wooli.in` or IP address)
     - FTP Username (e.g., `u123456789`)
     - FTP Password

2. **Add secrets to GitHub:**
   - Go to your repository: `https://github.com/paarrthchugh-3761/wooli.in`
   - Click **Settings** → **Secrets and variables** → **Actions**
   - Click **New repository secret**
   - Add these 3 secrets:

   | Secret Name | Value |
   |------------|-------|
   | `HOSTINGER_FTP_SERVER` | Your FTP server (e.g., `ftp.wooli.in`) |
   | `HOSTINGER_FTP_USERNAME` | Your FTP username |
   | `HOSTINGER_FTP_PASSWORD` | Your FTP password |

### Step 3: Create Branch Structure

```bash
# In your local terminal or use GitHub UI

# Create staging branch
git checkout -b staging
git push -u origin staging

# Switch back to main
git checkout main
```

---

## 🚀 How to Use

### For Testing Changes

1. Make changes in your code
2. Commit to **staging** branch:
   ```bash
   git add .
   git commit -m "Test: Updated blog styling"
   git push origin staging
   ```
3. Wait 2-3 minutes
4. Visit: `https://paarrthchugh-3761.github.io/wooli.in` to test

### For Production Deployment

1. Once tested on staging, merge to **main**:
   ```bash
   git checkout main
   git merge staging
   git push origin main
   ```
2. Wait 2-3 minutes
3. Your changes are LIVE on `https://wooli.in` 🎉

---

## 📝 Admin Panel Usage

### Creating Blog Posts

1. Go to: `https://wooli.in/admin`
2. Log in
3. Create blog post via the form
4. The PHP panel creates files in `posts/` folder
5. **Important:** To commit these changes to GitHub:

```bash
# Pull latest changes (includes new blog from admin panel)
git pull origin main

# Add new blog posts
git add posts/
git add blog.html

# Commit
git commit -m "Add new blog post: [Your Blog Title]"

# Push to staging first for testing
git push origin staging

# Once tested, push to main
git checkout main
git merge staging
git push origin main
```

---

## ⚠️ Important Notes

### Admin Panel Behavior

- **Staging (GitHub Pages):** Admin panel won't work (no PHP support)
- **Production (Hostinger):** Admin panel works perfectly
- Admin panel creates files directly on server
- You need to manually commit admin-created files to GitHub

### Deployment Flow

1. **Code changes** (HTML/CSS/JS) → Push to staging → Test → Merge to main
2. **Blog posts via admin** → Created on Hostinger → Pull to local → Commit to GitHub

### Troubleshooting

**Deployment failed:**
- Check GitHub Actions tab for error logs
- Verify FTP credentials in Secrets

**Staging not updating:**
- Check if GitHub Pages is enabled
- Wait 2-3 minutes for build to complete

**Production not updating:**
- Verify FTP credentials are correct
- Check Hostinger FTP access is enabled
- Check GitHub Actions logs

---

## 🔒 Security

- FTP credentials are stored as **encrypted GitHub Secrets**
- Never commit credentials to code
- Admin panel should have strong password
- Consider adding IP whitelist for /admin access in Hostinger

---

## 📊 Monitoring Deployments

View deployment status:
- Go to: `https://github.com/paarrthchugh-3761/wooli.in/actions`
- Green checkmark ✅ = Success
- Red X ❌ = Failed (click for logs)

---

## 🎯 Quick Reference

| Task | Command |
|------|---------|
| Test changes | `git push origin staging` |
| Deploy to production | `git push origin main` |
| View staging | `https://paarrthchugh-3761.github.io/wooli.in` |
| View production | `https://wooli.in` |
| Admin panel | `https://wooli.in/admin` |

