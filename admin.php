<?php
// admin.php — Al-Shifah Charity Foundation Admin Panel
session_start();
require_once 'includes/data.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php"); exit;
}

$pageTitle = "Admin Panel";
$tab = $_GET['tab'] ?? 'dashboard';
$msg = '';
$msgType = 'success';

// ── POST HANDLERS ──────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Settings
    if (isset($_POST['update_settings'])) {
        foreach ($_POST['settings'] as $k => $v) {
            $pdo->prepare("UPDATE site_settings SET setting_value=? WHERE setting_key=?")->execute([$v,$k]);
        }
        $msg = "Settings saved.";
    }

    // Objectives
    if (isset($_POST['update_objectives'])) {
        foreach ($_POST['objectives'] as $id => $d) {
            $pdo->prepare("UPDATE objectives SET title=?,description=? WHERE id=?")->execute([$d['title'],$d['description'],$id]);
        }
        $msg = "Objectives updated.";
    }

    // Impact Stats
    if (isset($_POST['update_stats'])) {
        foreach ($_POST['stats'] as $id => $d) {
            $pdo->prepare("UPDATE impact_stats SET stat_value=?,label=? WHERE id=?")->execute([$d['value'],$d['label'],$id]);
        }
        $msg = "Impact stats updated.";
    }

    // Campaigns
    if (isset($_POST['campaign_action'])) {
        if ($_POST['campaign_action'] === 'delete') {
            $pdo->prepare("DELETE FROM campaigns WHERE id=?")->execute([$_POST['campaign_id']]);
            $msg = "Campaign deleted.";
        } elseif ($_POST['campaign_action'] === 'save') {
            $id = $_POST['campaign_id'] ?? null;
            $vals = [$_POST['title'],$_POST['description'],$_POST['goal_amount'],$_POST['category'],$_POST['image_url'],$_POST['thumbnail_url']];
            if ($id) {
                $pdo->prepare("UPDATE campaigns SET title=?,description=?,goal_amount=?,category=?,image_url=?,thumbnail_url=? WHERE id=?")->execute([...$vals, $id]);
            } else {
                $pdo->prepare("INSERT INTO campaigns (title,description,goal_amount,category,image_url,thumbnail_url) VALUES(?,?,?,?,?,?)")->execute($vals);
            }
            $msg = "Campaign saved.";
        }
    }

    // Blog
    if (isset($_POST['blog_action'])) {
        if ($_POST['blog_action'] === 'delete') {
            $pdo->prepare("DELETE FROM blog_posts WHERE id=?")->execute([$_POST['blog_id']]);
            $msg = "Post deleted.";
        } elseif ($_POST['blog_action'] === 'save') {
            $id = $_POST['blog_id'] ?? null;
            $vals = [$_POST['title'],$_POST['excerpt'],$_POST['content'],$_POST['category'],$_POST['image_url'],$_POST['thumbnail_url']];
            if ($id) {
                $pdo->prepare("UPDATE blog_posts SET title=?,excerpt=?,content=?,category=?,image_url=?,thumbnail_url=? WHERE id=?")->execute([...$vals,$id]);
            } else {
                $pdo->prepare("INSERT INTO blog_posts (title,excerpt,content,category,image_url,thumbnail_url,post_date) VALUES(?,?,?,?,?,?,?)")->execute([...$vals,date('M j, Y')]);
            }
            $msg = "Post saved.";
        }
    }

    // Gallery
    if (isset($_POST['gallery_action'])) {
        if ($_POST['gallery_action'] === 'delete') {
            $pdo->prepare("DELETE FROM gallery WHERE id=?")->execute([$_POST['gallery_id']]);
            $msg = "Photo removed.";
        } elseif ($_POST['gallery_action'] === 'add') {
            $pdo->prepare("INSERT INTO gallery (caption,image_url) VALUES(?,?)")->execute([$_POST['title'],$_POST['image_url']]);
            $msg = "Photo added.";
        }
    }

    // Financial
    if (isset($_POST['financial_action'])) {
        if ($_POST['financial_action'] === 'delete') {
            $pdo->prepare("DELETE FROM financial_data WHERE id=?")->execute([$_POST['financial_id']]);
            $msg = "Record removed.";
        } elseif ($_POST['financial_action'] === 'add') {
            $pdo->prepare("INSERT INTO financial_data (fiscal_year,category,amount,usage_context) VALUES(?,?,?,?)")->execute([$_POST['fiscal_year'],$_POST['category'],$_POST['amount'],$_POST['usage_context']]);
            $msg = "Record added.";
        }
    }

    // Messages
    if (isset($_POST['message_action'])) {
        $id = $_POST['message_id'];
        if ($_POST['message_action'] === 'delete') {
            $pdo->prepare("DELETE FROM messages WHERE id=?")->execute([$id]);
            $msg = "Message deleted.";
        } elseif ($_POST['message_action'] === 'read') {
            $pdo->prepare("UPDATE messages SET status='read' WHERE id=?")->execute([$id]);
        }
    }

    // Broadcast
    if (isset($_POST['broadcast_action'])) {
        $pdo->prepare("INSERT INTO broadcasts (subject,content,target_group,sent_by) VALUES(?,?,?,?)")
            ->execute([$_POST['subject'],$_POST['content'],$_POST['target_group'],$_SESSION['admin_id']??1]);
        $msg = "Broadcast sent.";
    }

    // CMS: Hero
    if (isset($_POST['hero_action'])) {
        $pg = $_POST['page_name'] ?? 'home';
        $f = ['hero_type','title','subtitle','badge_text','image_url','video_url','overlay_opacity','btn_primary_text','btn_primary_url','btn_secondary_text','btn_secondary_url'];
        $vals = array_map(fn($k) => $_POST[$k] ?? '', $f);
        $vals[] = $pg;
        $pdo->prepare("UPDATE page_content SET hero_type=?,title=?,subtitle=?,badge_text=?,image_url=?,video_url=?,overlay_opacity=?,btn_primary_text=?,btn_primary_url=?,btn_secondary_text=?,btn_secondary_url=? WHERE page_name=? AND section_name='hero'")->execute($vals);
        $msg = ucfirst($pg) . " hero saved.";
    }

    // CMS: Slides
    if (isset($_POST['slide_action'])) {
        if ($_POST['slide_action'] === 'add') {
            $pdo->prepare("INSERT INTO hero_slides (page_name,image_url,caption,order_rank) VALUES('home',?,?,(SELECT COALESCE(MAX(order_rank),0)+1 FROM hero_slides s))")->execute([$_POST['slide_url'],$_POST['slide_caption']??'']);
            $msg = "Slide added.";
        } elseif ($_POST['slide_action'] === 'delete') {
            $pdo->prepare("DELETE FROM hero_slides WHERE id=?")->execute([$_POST['slide_id']]);
            $msg = "Slide removed.";
        }
    }

    // CMS: Homepage sections
    if (isset($_POST['sections_action'])) {
        foreach ($_POST['sections'] ?? [] as $key => $d) {
            $pdo->prepare("UPDATE homepage_sections SET is_visible=?,order_rank=? WHERE section_key=?")
                ->execute([isset($d['visible'])?1:0,(int)($d['order']??0),$key]);
        }
        $msg = "Homepage layout saved.";
    }

    // CMS: Content blocks
    if (isset($_POST['blocks_action'])) {
        foreach ($_POST['blocks'] as $sec => $keys)
            foreach ($keys as $k => $v)
                $pdo->prepare("INSERT INTO content_blocks (section_key,block_key,block_value) VALUES(?,?,?) ON DUPLICATE KEY UPDATE block_value=VALUES(block_value)")->execute([$sec,$k,$v]);
        $msg = "Content saved.";
    }

    // CMS: About blocks
    if (isset($_POST['about_blocks_action'])) {
        foreach ($_POST['about_blocks'] as $k => $v)
            $pdo->prepare("INSERT INTO content_blocks (section_key,block_key,block_value) VALUES('about',?,?) ON DUPLICATE KEY UPDATE block_value=VALUES(block_value)")->execute([$k,$v]);
        $msg = "About page content saved.";
    }

    // CMS: Team
    if (isset($_POST['team_action'])) {
        if ($_POST['team_action'] === 'delete') {
            $pdo->prepare("DELETE FROM team_members WHERE id=?")->execute([$_POST['team_id']]);
            $msg = "Team member removed.";
        } elseif ($_POST['team_action'] === 'save') {
            $id = $_POST['team_id'] ?: null;
            $d = [$_POST['name'],$_POST['role'],$_POST['bio']??'',$_POST['photo_url']??'',$_POST['type']??'Trustee'];
            if ($id) $pdo->prepare("UPDATE team_members SET name=?,role=?,bio=?,photo_url=?,type=? WHERE id=?")->execute([...$d,$id]);
            else $pdo->prepare("INSERT INTO team_members (name,role,bio,photo_url,type,order_rank) VALUES(?,?,?,?,?,(SELECT COALESCE(MAX(order_rank),0)+1 FROM team_members t))")->execute($d);
            $msg = "Team member saved.";
        }
    }

    // CMS: Contact settings
    if (isset($_POST['contact_settings_action'])) {
        foreach ($_POST['contact'] ?? [] as $k => $v)
            $pdo->prepare("INSERT INTO contact_settings (setting_key,setting_value) VALUES(?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)")->execute([$k,$v]);
        foreach ($_POST['contact_blocks'] ?? [] as $k => $v)
            $pdo->prepare("INSERT INTO content_blocks (section_key,block_key,block_value) VALUES('contact',?,?) ON DUPLICATE KEY UPDATE block_value=VALUES(block_value)")->execute([$k,$v]);
        $msg = "Contact settings saved.";
    }

    // CMS: Nav/Footer
    if (isset($_POST['nav_settings_action'])) {
        foreach ($_POST['nav_settings'] ?? [] as $k => $v)
            $pdo->prepare("UPDATE site_settings SET setting_value=? WHERE setting_key=?")->execute([$v,$k]);
        foreach ($_POST['footer_blocks'] ?? [] as $k => $v)
            $pdo->prepare("INSERT INTO content_blocks (section_key,block_key,block_value) VALUES('footer',?,?) ON DUPLICATE KEY UPDATE block_value=VALUES(block_value)")->execute([$k,$v]);
        $msg = "Navigation & footer saved.";
    }
}

// Re-fetch
require 'includes/data.php';
$totalDonors  = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$unreadMsgs   = $pdo->query("SELECT COUNT(*) FROM messages WHERE status='unread'")->fetchColumn();
$totalRaised  = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM donations")->fetchColumn();
$activeCamps  = $pdo->query("SELECT COUNT(*) FROM campaigns WHERE is_active=1")->fetchColumn();

include_once 'includes/header.php';
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root { --emerald:#10b981; --emerald-dark:#059669; --sidebar:#ffffff; --bg:#f8fafc; --surface:#ffffff; --border:#e2e8f0; --text:#0f172a; --muted:#64748b; }
  * { font-family:'Inter',sans-serif; }
  body { background:var(--bg); color:var(--text); margin:0; padding:0; }
  /* Sidebar */
  #sidebar { width:240px; height:100vh; background:var(--sidebar); box-shadow: 1px 0 10px rgba(0,0,0,0.02); border-right:1px solid var(--border); position:fixed; top:0; left:0; z-index:50; display:flex; flex-direction:column; transition:transform .3s; }
  #sidebar.collapsed { transform:translateX(-240px); }
  .sidebar-logo { padding:24px 20px 20px; border-bottom:1px solid rgba(0,0,0,0.03); }
  .sidebar-logo .brand { display:flex; align-items:center; gap:12px; text-decoration:none; }
  .sidebar-logo .icon { width:38px; height:38px; background:linear-gradient(135deg,var(--emerald),var(--emerald-dark)); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 4px 10px rgba(16,185,129,0.2); }
  .sidebar-logo .name { font-size:15px; font-weight:700; color:var(--text); line-height:1.2; letter-spacing:-0.01em; }
  .sidebar-logo .sub { font-size:11px; color:var(--muted); font-weight:500; }
  .sidebar-nav { flex:1; overflow-y:auto; padding:12px 12px; }
  .nav-group-label { font-size:11px; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.05em; padding:14px 10px 6px; }
  .nav-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:8px; font-size:13px; font-weight:500; color:var(--muted); text-decoration:none; transition:all .15s; margin-bottom:2px; }
  .nav-item:hover { background:#f1f5f9; color:var(--text); }
  .nav-item.active { background:#ecfdf5; color:var(--emerald-dark); font-weight:600; }
  .nav-item .icon { width:18px; height:18px; flex-shrink:0; }
  .nav-item .badge { margin-left:auto; background:#ef4444; color:#fff; font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px; }
  .sidebar-footer { padding:16px 16px; border-top:1px solid rgba(0,0,0,0.03); }
  /* Main */
  #main { margin-left:240px; min-height:100vh; display:flex; flex-direction:column; background:var(--bg); }
  /* Topbar */
  #topbar { position:sticky; top:0; z-index:30; background:rgba(255,255,255,0.9); backdrop-filter:blur(10px); display:flex; align-items:center; justify-content:space-between; padding:0 32px; height:64px; }
  #topbar .page-title { font-size:18px; font-weight:700; color:var(--text); letter-spacing:-0.01em; }
  #topbar .actions { display:flex; align-items:center; gap:16px; }
  /* Content */
  #content { flex:1; padding:32px; max-width:1400px; }
  /* Cards */
  .card { background:#fff; border:1px solid rgba(0,0,0,0.05); border-radius:16px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.01); overflow:hidden; }
  .card-header { padding:20px 24px; border-bottom:1px solid rgba(0,0,0,0.03); display:flex; align-items:center; justify-content:space-between; }
  .card-header h2 { font-size:16px; font-weight:700; color:var(--text); margin:0; letter-spacing:-0.01em; }
  .card-header .sub { font-size:13px; color:var(--muted); margin-top:2px; }
  .card-body { padding:24px; }
  /* Stat cards */
  .stat-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:20px; margin-bottom:24px; }
  .stat-card { background:#fff; border:1px solid rgba(0,0,0,0.05); border-radius:16px; padding:20px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.02); }
  .stat-card .label { font-size:12px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.04em; }
  .stat-card .value { font-size:32px; font-weight:800; color:var(--text); margin:8px 0 0; line-height:1; letter-spacing:-0.02em; }
  .stat-card .icon-wrap { float:right; width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; }
  /* Tables */
  .data-table { width:100%; border-collapse:collapse; font-size:14px; }
  .data-table th { text-align:left; padding:14px 20px; font-size:12px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid rgba(0,0,0,0.05); background:#f8fafc; }
  .data-table td { padding:14px 20px; border-bottom:1px solid rgba(0,0,0,0.02); color:var(--text); vertical-align:middle; line-height:1.5; }
  .data-table tr:last-child td { border-bottom:none; }
  .data-table tr:hover td { background:#f8fafc; }
  /* Forms */
  .form-label { display:block; font-size:12px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.04em; margin-bottom:8px; }
  .form-input { width:100%; border:1px solid #cbd5e1; border-radius:10px; padding:10px 14px; font-size:14px; font-weight:500; color:var(--text); background:#fff; outline:none; transition:all .2s; box-sizing:border-box; }
  .form-input:focus { border-color:var(--emerald); box-shadow:0 0 0 4px rgba(16,185,129,.1); }
  textarea.form-input { resize:vertical; min-height:100px; }
  .form-select { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; padding-right:36px; }
  .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
  .form-group { margin-bottom:20px; }
  /* Buttons */
  .btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:10px 20px; border-radius:10px; font-size:14px; font-weight:600; border:none; cursor:pointer; transition:all .2s; text-decoration:none; }
  .btn-primary { background:var(--emerald); color:#fff; box-shadow:0 4px 6px -1px rgba(16,185,129,.2); }
  .btn-primary:hover { background:var(--emerald-dark); box-shadow:0 6px 10px -1px rgba(16,185,129,.3); transform:translateY(-1px); }
  .btn-secondary { background:#f1f5f9; color:var(--text); border:1px solid #e2e8f0; }
  .btn-secondary:hover { background:#e2e8f0; }
  .btn-danger { background:#fef2f2; color:#dc2626; border:1px solid #fee2e2; }
  .btn-danger:hover { background:#fee2e2; }
  .btn-sm { padding:6px 12px; font-size:13px; border-radius:8px; }
  .btn-icon { width:34px; height:34px; padding:0; border-radius:8px; }
  /* Alerts */
  .alert { padding:14px 20px; border-radius:12px; font-size:14px; font-weight:500; margin-bottom:24px; display:flex; align-items:center; gap:10px; box-shadow:0 2px 4px rgba(0,0,0,0.02); }
  .alert-success { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
  .alert-error { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
  /* Upload widget */
  .upload-widget { border:2px dashed #cbd5e1; border-radius:12px; padding:24px; background:#f8fafc; transition:all .2s; }
  .upload-widget .tabs { display:flex; gap:4px; margin-bottom:16px; background:#e2e8f0; border-radius:8px; padding:4px; }
  .upload-widget .tab-btn { flex:1; padding:8px; border-radius:6px; border:none; background:none; font-size:13px; font-weight:600; color:var(--muted); cursor:pointer; transition:all .2s; }
  .upload-widget .tab-btn.active { background:#fff; color:var(--text); box-shadow:0 1px 3px rgba(0,0,0,.1); }
  .upload-widget .drop-zone { border:2px dashed #cbd5e1; border-radius:10px; padding:32px 20px; text-align:center; cursor:pointer; background:#fff; transition:all .2s; }
  .upload-widget .drop-zone:hover,.upload-widget .drop-zone.dragover { border-color:var(--emerald); background:#f0fdf4; border: 2px dashed var(--emerald); }
  .upload-widget .preview-area { display:flex; align-items:center; gap:12px; margin-top:16px; background:#fff; padding:12px; border-radius:10px; border:1px solid #e2e8f0; }
  .upload-widget .preview-thumb { width:56px; height:56px; object-fit:cover; border-radius:8px; border:1px solid var(--border); }
  .upload-widget .preview-name { font-size:13px; font-weight:500; color:var(--text); flex:1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  /* Badge */
  .badge-pill { display:inline-flex; align-items:center; padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600; }
  .badge-green { background:#dcfce7; color:#15803d; }
  .badge-blue { background:#dbeafe; color:#1d4ed8; }
  .badge-yellow { background:#fef9c3; color:#a16207; }
  .badge-red { background:#fee2e2; color:#b91c1c; }
  /* Toggle switch */
  .toggle { position:relative; display:inline-block; width:44px; height:24px; }
  .toggle input { display:none; }
  .toggle-slider { position:absolute; inset:0; background:#cbd5e1; border-radius:24px; cursor:pointer; transition:all .2s; }
  .toggle input:checked + .toggle-slider { background:var(--emerald); }
  .toggle-slider::before { content:''; position:absolute; width:18px; height:18px; background:#fff; border-radius:50%; left:3px; top:3px; transition:all .2s; box-shadow:0 1px 2px rgba(0,0,0,0.1); }
  .toggle input:checked + .toggle-slider::before { transform:translateX(20px); }
  /* Section header */
  .section-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; gap:20px; }
  .section-title { font-size:24px; font-weight:800; color:var(--text); margin:0; letter-spacing:-0.02em; }
  .section-sub { font-size:14px; font-weight:500; color:var(--muted); margin:4px 0 0; }
  @media(max-width:768px) { #sidebar { transform:translateX(-240px); } #sidebar.open { transform:translateX(0); } #main { margin-left:0; } .form-grid { grid-template-columns:1fr; } #topbar { padding:0 20px; } #content { padding:20px; } }
</style>

<!-- ═══ SIDEBAR ═══ -->
<aside id="sidebar">
  <div class="sidebar-logo">
    <a href="index.php" class="brand">
      <div class="icon"><svg width="20" height="20" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 2l9 4.5V12c0 5.25-4.05 9.75-9 10.5C3.05 21.75-1 17.25-1 12V6.5L12 2z"/></svg></div>
      <div><div class="name">Al-Shifah</div><div class="sub">Admin Center</div></div>
    </a>
  </div>

  <nav class="sidebar-nav">
    <?php
    $nav = [
      'Dashboard' => [
        ['tab'=>'dashboard','label'=>'Dashboard','icon'=>'layout-grid'],
        ['tab'=>'treasury','label'=>'Treasury','icon'=>'briefcase'],
        ['tab'=>'reports','label'=>'Reports','icon'=>'pie-chart'],
        ['tab'=>'finance','label'=>'Finance','icon'=>'bar-chart-2'],
        ['tab'=>'messages','label'=>'Messages','icon'=>'mail','badge'=>$unreadMsgs],
      ],
      'Content' => [
        ['tab'=>'campaigns','label'=>'Campaigns','icon'=>'flag'],
        ['tab'=>'blogs','label'=>'Blog Posts','icon'=>'file-text'],
        ['tab'=>'gallery','label'=>'Gallery','icon'=>'image'],
        ['tab'=>'broadcast','label'=>'Broadcast','icon'=>'radio'],
      ],
      'Admin' => [
        ['tab'=>'users','label'=>'Donors','icon'=>'users'],
        ['tab'=>'components','label'=>'Impact Stats','icon'=>'trending-up'],
        ['tab'=>'objectives','label'=>'Objectives','icon'=>'target'],
        ['tab'=>'settings','label'=>'Settings','icon'=>'settings'],
      ],
      'Website' => [
        ['tab'=>'cms_homepage','label'=>'Homepage','icon'=>'home'],
        ['tab'=>'cms_hero','label'=>'Hero Section','icon'=>'layers'],
        ['tab'=>'cms_about','label'=>'About Page','icon'=>'info'],
        ['tab'=>'cms_contact','label'=>'Contact Page','icon'=>'phone'],
        ['tab'=>'cms_nav','label'=>'Navigation & Footer','icon'=>'menu'],
      ],
    ];
    foreach ($nav as $group => $items): ?>
      <div class="nav-group-label"><?php echo $group; ?></div>
      <?php foreach ($items as $it): ?>
        <a href="?tab=<?php echo $it['tab']; ?>" class="nav-item <?php echo $tab===$it['tab']?'active':''; ?>">
          <i data-lucide="<?php echo $it['icon']; ?>" class="icon"></i>
          <?php echo $it['label']; ?>
          <?php if (!empty($it['badge']) && $it['badge'] > 0): ?>
            <span class="badge"><?php echo $it['badge']; ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </nav>

  <div class="sidebar-footer">
    <a href="logout.php" class="btn btn-secondary" style="width:100%;justify-content:center;">
      <i data-lucide="log-out" style="width:14px;height:14px;"></i> Sign Out
    </a>
  </div>
</aside>

<!-- ═══ MAIN ═══ -->
<div id="main">
  <!-- Topbar -->
  <div id="topbar">
    <div style="display:flex;align-items:center;gap:12px;">
      <button id="menu-btn" onclick="document.getElementById('sidebar').classList.toggle('open')" style="display:none;background:none;border:none;cursor:pointer;padding:4px;">
        <i data-lucide="menu" style="width:20px;height:20px;"></i>
      </button>
      <span class="page-title"><?php
        $titles=['dashboard'=>'Dashboard','finance'=>'Finance','messages'=>'Messages','campaigns'=>'Campaigns','blogs'=>'Blog Posts','gallery'=>'Gallery','broadcast'=>'Broadcast','users'=>'Donors','components'=>'Impact Stats','objectives'=>'Objectives','settings'=>'Settings','cms_homepage'=>'Homepage Editor','cms_hero'=>'Hero Section','cms_about'=>'About Page','cms_contact'=>'Contact Page','cms_nav'=>'Navigation & Footer'];
        echo $titles[$tab] ?? ucfirst($tab);
      ?></span>
    </div>
    <div class="actions">
      <a href="index.php" target="_blank" class="btn btn-sm btn-secondary" style="border-radius:20px;padding:4px 12px;font-size:11px;color:var(--text);border:1px solid var(--border);text-decoration:none;"><i data-lucide="external-link" style="width:12px;height:12px;margin-right:4px;"></i> View Site</a>
      <span style="font-size:12px;color:var(--muted);"><?php echo date('M j, Y'); ?></span>
      <div style="width:32px;height:32px;background:#ecfdf5;border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:var(--emerald-dark);">
        <?php echo strtoupper(substr($_SESSION['admin_username'],0,1)); ?>
      </div>
    </div>
  </div>

  <!-- Content -->
  <div id="content">
    <?php if ($msg): ?>
      <div class="alert alert-success"><i data-lucide="check-circle" style="width:15px;height:15px;flex-shrink:0;"></i><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>
    <?php include 'includes/admin_tabs.php'; ?>
  </div>
</div>
  </div>
</div>

<script>
// Lucide icons
if(typeof lucide!=='undefined') lucide.createIcons();

// Upload widget logic
function initUploadWidget(widgetId, fieldId, accept, folder) {
  const w = document.getElementById(widgetId);
  if (!w) return;
  const drop = w.querySelector('.drop-zone');
  const fileInput = w.querySelector('input[type=file]');
  const urlInput = w.querySelector('.url-input');
  const field = document.getElementById(fieldId);
  const preview = w.querySelector('.preview-area');
  const previewImg = w.querySelector('.preview-thumb');
  const previewIcon = w.querySelector('.preview-icon');
  const previewName = w.querySelector('.preview-name');

  // Tab switching
  w.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      w.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');
      w.querySelector('.upload-panel').style.display = btn.dataset.panel==='upload'?'block':'none';
      w.querySelector('.url-panel').style.display   = btn.dataset.panel==='url'?'block':'none';
    });
  });

  // Drop zone click
  if (drop) drop.addEventListener('click', () => fileInput && fileInput.click());

  // Drag events
  if (drop) {
    drop.addEventListener('dragover', e => { e.preventDefault(); drop.classList.add('dragover'); });
    drop.addEventListener('dragleave', () => drop.classList.remove('dragover'));
    drop.addEventListener('drop', e => { e.preventDefault(); drop.classList.remove('dragover'); handleFile(e.dataTransfer.files[0]); });
  }

  // File input change
  if (fileInput) fileInput.addEventListener('change', () => handleFile(fileInput.files[0]));

  // URL input change
  if (urlInput) urlInput.addEventListener('input', () => {
    field.value = urlInput.value;
    showPreview(urlInput.value, urlInput.value.split('/').pop());
  });

  function handleFile(file) {
    if (!file) return;
    const fd = new FormData();
    fd.append('file', file);
    const oldHtml = drop.innerHTML;
    drop.innerHTML = '<span style="font-size:12px;color:var(--muted);">Uploading...</span>';
    fetch('includes/upload.php', {method:'POST',body:fd})
      .then(r=>r.json())
      .then(data => {
        drop.innerHTML = oldHtml;
        const newFileInput = drop.querySelector('input[type=file]');
        if (newFileInput) newFileInput.addEventListener('change', () => handleFile(newFileInput.files[0]));
        
        if (data.success) {
          field.value = data.url;
          showPreview(data.url, data.name);
        } else {
          alert('Upload Error: ' + data.error);
        }
      })
      .catch(err => {
        drop.innerHTML = oldHtml;
        const newFileInput = drop.querySelector('input[type=file]');
        if (newFileInput) newFileInput.addEventListener('change', () => handleFile(newFileInput.files[0]));
        alert('Upload failed.');
      });
  }

  window.clearUpload = function(fieldId, widgetId) {
    const field = document.getElementById(fieldId);
    if (field) field.value = '';
    const w = document.getElementById(widgetId);
    if (w) {
      const preview = w.querySelector('.preview-area');
      if (preview) preview.style.display = 'none';
      const urlInput = w.querySelector('.url-input');
      if (urlInput) urlInput.value = '';
      const previewImg = w.querySelector('.preview-thumb');
      if (previewImg) { previewImg.src = ''; previewImg.style.display = 'none'; }
      const previewIcon = w.querySelector('.preview-icon');
      if (previewIcon) previewIcon.style.display = 'block';
    }
  };

  function showPreview(url, name) {
    if (!preview) return;
    preview.style.display = 'flex';
    if (url.match(/\.(jpg|jpeg|png|gif|webp|svg)$/i) || url.startsWith('http') || url.startsWith('data:')) {
      if (previewImg) { previewImg.src = url; previewImg.style.display='block'; }
      if (previewIcon) previewIcon.style.display='none';
    } else {
      if (previewImg) { previewImg.src = ''; previewImg.style.display='none'; }
      if (previewIcon) previewIcon.style.display='block';
    }
    if (previewName) previewName.textContent = name || url;
  }

  // Init existing value
  if (field && field.value) showPreview(field.value, field.value.split('/').pop());
}

// Toggle hero mode visibility for video field
function toggleHeroMode(sel, pg) {
  const vf = document.getElementById('vf_'+pg);
  if (vf) vf.style.display = sel.value==='video' ? 'block' : 'none';
}



// Initialize all upload widgets on page
document.querySelectorAll('[data-upload-widget]').forEach(w => {
  const id = w.id;
  const fieldId = w.dataset.field;
  initUploadWidget(id, fieldId);
});
</script>

