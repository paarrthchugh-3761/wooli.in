# Wooli.in - Simple Deployment Setup

## 🚀 Super Simple Workflow

```
You → Paste blog content to Claude
      ↓
Claude → Creates blog post files + updates blog.html
      ↓
You → git push origin main
      ↓
GitHub Actions → Auto-deploys to Hostinger
```

---

## 📝 How to Create a New Blog Post

### Step 1: Prepare Your Content

Format your blog content like this:

```
Title: Interest Rates Explained for Indian Beginners (2025)
Category: Personal Finance
Reading Time: 11 min read
Description: RBI hiked interest rates sounds boring until you realize it just increased your home loan EMI by ₹5,000/month.

## What Are Interest Rates?

Interest rate is the price you pay to borrow money.

### The Borrower's Nightmare

Your cousin's real story:
- Took ₹30 lakh home loan in 2021
- EMI was ₹20,400/month

**Bold text** for emphasis.
```

### Step 2: Give to Claude

Just paste the content and say:
> "Create a new blog post with this content"

Claude will:
- Create `posts/[slug]/index.html`
- Add thumbnail placeholder
- Update `blog.html` with the card
- Commit everything

### Step 3: Add Thumbnail

Upload your thumbnail as `posts/[slug]/thumbnail.png`

### Step 4: Push to GitHub

```bash
git push origin main
```

Done! Auto-deploys to Hostinger in 2-3 minutes.

---

## ⚙️ One-Time Setup

### 1. Add Hostinger FTP Credentials to GitHub

1. Get FTP credentials from Hostinger:
   - Log into Hostinger → Hosting → FTP Accounts
   - Note: Server, Username, Password

2. Add to GitHub Secrets:
   - Go to: `https://github.com/paarrthchugh-3761/wooli.in/settings/secrets/actions`
   - Add these 3 secrets:
     - `HOSTINGER_FTP_SERVER` (e.g., `ftp.wooli.in`)
     - `HOSTINGER_FTP_USERNAME`
     - `HOSTINGER_FTP_PASSWORD`

### 2. Done!

That's it. Now every push to `main` auto-deploys to Hostinger.

---

## 🛠️ Manual Deployment (If GitHub Actions Fails)

Download repo as ZIP → Upload to Hostinger File Manager → Extract to `public_html/`

---

## 📊 Check Deployment Status

Go to: `https://github.com/paarrthchugh-3761/wooli.in/actions`

- Green ✅ = Deployed successfully
- Red ❌ = Failed (check logs)

