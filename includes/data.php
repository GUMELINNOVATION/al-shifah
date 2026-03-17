<?php
// includes/data.php
require_once 'db.php';

// ─── Helper Functions ───────────────────────────────────────────────────────

if (!function_exists('getSetting')) {
    function getSetting($key, $pdo) {
        $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : null;
    }
}

if (!function_exists('getBlock')) {
    /** Get a content block value, with optional default */
    function getBlock($section, $key, $pdo, $default = '') {
        $stmt = $pdo->prepare("SELECT block_value FROM content_blocks WHERE section_key = ? AND block_key = ?");
        $stmt->execute([$section, $key]);
        $result = $stmt->fetch();
        return ($result && $result['block_value'] !== null) ? $result['block_value'] : $default;
    }
}

if (!function_exists('getContactSetting')) {
    function getContactSetting($key, $pdo) {
        $stmt = $pdo->prepare("SELECT setting_value FROM contact_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : null;
    }
}

// ─── Site Settings ──────────────────────────────────────────────────────────

$OFFICIAL_INFO = [
    'address'          => getSetting('address', $pdo),
    'chairman'         => getSetting('chairman', $pdo),
    'secretary'        => getSetting('secretary', $pdo),
    'trustees'         => [],
    'registrationDate' => getSetting('registration_date', $pdo),
    'type'             => 'Not-for-profit and Non-political',
    'email'            => getSetting('contact_email', $pdo),
    'phones'           => [getSetting('contact_phone_1', $pdo), getSetting('contact_phone_2', $pdo)],
    'paystackPublicKey'=> getSetting('paystack_public_key', $pdo),
    'siteName'         => getSetting('site_name', $pdo),
    'facebook'         => getSetting('facebook_url', $pdo),
    'twitter'          => getSetting('twitter_url', $pdo),
    'instagram'        => getSetting('instagram_url', $pdo),
];

// Fetch Trustees
$stmt = $pdo->query("SELECT name FROM team_members WHERE type = 'Trustee' ORDER BY order_rank ASC");
$OFFICIAL_INFO['trustees'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

// ─── Hero Config ────────────────────────────────────────────────────────────

$HERO_CONFIG = [];
$heroPages = ['home', 'about', 'contact'];
foreach ($heroPages as $pg) {
    $stmt = $pdo->prepare("SELECT * FROM page_content WHERE page_name = ? AND section_name = 'hero' LIMIT 1");
    $stmt->execute([$pg]);
    $row = $stmt->fetch();
    if ($row) {
        $HERO_CONFIG[$pg] = $row;
        // Fetch slides for this page
        $sStmt = $pdo->prepare("SELECT * FROM hero_slides WHERE page_name = ? ORDER BY order_rank ASC");
        $sStmt->execute([$pg]);
        $HERO_CONFIG[$pg]['slides'] = $sStmt->fetchAll();
    }
}

// ─── Homepage Section Order & Visibility ────────────────────────────────────

$stmt = $pdo->query("SELECT * FROM homepage_sections ORDER BY order_rank ASC");
$HOME_SECTIONS = $stmt->fetchAll();

// Build associative map for easy lookup
$HOME_SECTIONS_MAP = [];
foreach ($HOME_SECTIONS as $s) {
    $HOME_SECTIONS_MAP[$s['section_key']] = $s;
}

// ─── Content Blocks ─────────────────────────────────────────────────────────

// Load all blocks at once into a nested array
$stmt = $pdo->query("SELECT section_key, block_key, block_value FROM content_blocks");
$BLOCKS_RAW = $stmt->fetchAll();
$CONTENT_BLOCKS = [];
foreach ($BLOCKS_RAW as $b) {
    $CONTENT_BLOCKS[$b['section_key']][$b['block_key']] = $b['block_value'];
}

// ─── Contact Settings ────────────────────────────────────────────────────────

$CONTACT_SETTINGS = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM contact_settings");
    $rows = $stmt->fetchAll();
    foreach ($rows as $r) {
        $CONTACT_SETTINGS[$r['setting_key']] = $r['setting_value'];
    }
} catch (Exception $e) {
    // Fallback to site_settings if contact_settings doesn't exist yet
    $CONTACT_SETTINGS = [
        'email'        => getSetting('contact_email', $pdo),
        'phone_1'      => getSetting('contact_phone_1', $pdo),
        'phone_2'      => getSetting('contact_phone_2', $pdo),
        'address'      => getSetting('address', $pdo),
        'map_embed'    => '',
        'office_hours' => 'Mon - Fri: 9:00 AM – 5:00 PM',
    ];
}

// ─── Impact Stats ────────────────────────────────────────────────────────────

$stmt = $pdo->query("SELECT label, stat_value as value, icon FROM impact_stats ORDER BY order_rank ASC");
$IMPACT_STATS = $stmt->fetchAll();

// ─── Campaigns ───────────────────────────────────────────────────────────────

$stmt = $pdo->query("SELECT * FROM campaigns WHERE is_active = 1 ORDER BY created_at DESC");
$dbCampaigns = $stmt->fetchAll();
$CAMPAIGNS = array_map(function($c) use ($pdo) {
    $gStmt = $pdo->prepare("SELECT image_url FROM campaign_gallery WHERE campaign_id = ?");
    $gStmt->execute([$c['id']]);
    $gallery = $gStmt->fetchAll(PDO::FETCH_COLUMN);
    return [
        'id'            => (string)$c['id'],
        'title'         => $c['title'],
        'description'   => $c['description'],
        'longDescription'=> $c['long_description'],
        'goalAmount'    => (float)$c['goal_amount'],
        'currentAmount' => (float)$c['current_amount'],
        'image'         => $c['image_url'],
        'gallery'       => $gallery,
        'category'      => $c['category']
    ];
}, $dbCampaigns);

// ─── Objectives ──────────────────────────────────────────────────────────────

$stmt = $pdo->query("SELECT id, title, description as `desc` FROM objectives ORDER BY order_rank ASC");
$objectives = $stmt->fetchAll();

// ─── Team Members ─────────────────────────────────────────────────────────────

$stmt = $pdo->query("SELECT * FROM team_members ORDER BY order_rank ASC");
$TEAM_MEMBERS = $stmt->fetchAll();

// ─── Blog Posts ──────────────────────────────────────────────────────────────

$stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC");
$dbPosts = $stmt->fetchAll();
$BLOG_POSTS = array_map(function($p) {
    return [
        'id'       => (string)$p['id'],
        'title'    => $p['title'],
        'excerpt'  => $p['excerpt'],
        'content'  => $p['content'],
        'date'     => $p['post_date'],
        'image'    => $p['image_url'],
        'category' => $p['category']
    ];
}, $dbPosts);

// ─── Gallery ─────────────────────────────────────────────────────────────────

$stmt = $pdo->query("SELECT id, caption as title, image_url, created_at FROM gallery ORDER BY created_at DESC");
$GALLERY_ITEMS = $stmt->fetchAll();
$GALLERY_IMAGES = array_column($GALLERY_ITEMS, 'image_url');

// ─── Financial Data ───────────────────────────────────────────────────────────

$stmt = $pdo->query("SELECT fiscal_year as year, category, amount, usage_context FROM financial_data ORDER BY fiscal_year DESC, category ASC");
$FINANCIAL_DATA = $stmt->fetchAll();

// ─── Donation History ─────────────────────────────────────────────────────────

$stmt = $pdo->query("SELECT d.*, c.title as campaignTitle FROM donations d LEFT JOIN campaigns c ON d.campaign_id = c.id ORDER BY d.donation_date DESC");
$dbDonations = $stmt->fetchAll();
$MOCK_DONATION_HISTORY = array_map(function($d) {
    return [
        'id'            => (string)$d['id'],
        'donorName'     => $d['donor_name'],
        'amount'        => (float)$d['amount'],
        'date'          => $d['donation_date'],
        'campaignTitle' => $d['campaignTitle'],
        'isAnonymous'   => (bool)$d['is_anonymous']
    ];
}, $dbDonations);

// ─── Compatibility Functions ──────────────────────────────────────────────────

if (!function_exists('getCampaignById')) {
    function getCampaignById($id, $campaigns) {
        foreach ($campaigns as $campaign) {
            if ($campaign['id'] === (string)$id) return $campaign;
        }
        return null;
    }
}

if (!function_exists('getBlogPostById')) {
    function getBlogPostById($id, $posts) {
        foreach ($posts as $post) {
            if ($post['id'] === (string)$id) return $post;
        }
        return null;
    }
}
?>
