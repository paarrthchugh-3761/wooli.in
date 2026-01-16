<?php
session_start();
session_destroy();
header('Location: index.php');
exit;
?>
```

---

## **SETUP INSTRUCTIONS:**

**1. Create folder structure in Hostinger:**
```
public_html/
└── admin/
    ├── index.php
    ├── dashboard.php
    ├── process.php
    └── logout.php