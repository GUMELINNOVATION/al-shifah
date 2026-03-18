
<?php
// includes/navbar.php

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF'], ".php");
if ($currentPage == "index") $currentPage = "home";

// Intelligent path highlighting
$activeParent = $currentPage;
if (strpos($currentPage, 'campaign') !== false) $activeParent = 'campaigns';
if (strpos($currentPage, 'blog') !== false) $activeParent = 'blogs';
if (strpos($currentPage, 'transparency') !== false) $activeParent = 'transparency';

// Default navigation links
$navLinks = [
    ['name' => 'Home', 'id' => 'home', 'url' => 'index.php', 'icon' => 'home'],
    ['name' => 'About', 'id' => 'about', 'url' => 'about.php', 'icon' => 'info'],
    ['name' => 'Campaigns', 'id' => 'campaigns', 'url' => 'campaigns.php', 'icon' => 'megaphone'],
    ['name' => 'Transparency', 'id' => 'transparency', 'url' => 'transparency.php', 'icon' => 'pie-chart'],
    ['name' => 'Contact', 'id' => 'contact', 'url' => 'contact.php', 'icon' => 'mail'],
];

// Detect user/admin session
$user = null;
$isAdmin = false;

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $isAdmin = true;
}

if (isset($_SESSION['is_user_logged_in']) && $_SESSION['is_user_logged_in'] === true) {
    $user = [
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email']
    ];

    // Add profile link for logged in users
    $navLinks[] = [
        'name' => 'Profile',
        'id' => 'profile',
        'url' => 'profile.php',
        'icon' => 'user'
    ];
}
?>

<nav id="navbar" class="relative sticky top-0 z-50 transition-all duration-300 bg-white/80 backdrop-blur-xl border-b border-slate-100/50">
<div class="max-w-7xl mx-auto px-3 sm:px-5 lg:px-8">

<div class="flex justify-between h-16 lg:h-20 items-center">

<!-- Logo -->
<a href="index.php" class="flex items-center gap-2 sm:gap-3 group shrink-0">

<div class="relative">
<div class="absolute inset-0 bg-emerald-400 rounded-2xl blur-xl opacity-0 group-hover:opacity-30 transition-opacity"></div>

<div class="relative w-10 h-10 sm:w-11 sm:h-11 text-white bg-emerald-600 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200 group-hover:scale-105 group-hover:rotate-3 transition-all duration-300">
<i data-lucide="heart" class="w-5 h-5 sm:w-6 sm:h-6 fill-white"></i>
</div>
</div>

<div class="flex flex-col">
<span class="font-bold text-lg sm:text-xl tracking-tight text-slate-900 leading-none">
Al-Shifah
</span>
<span class="text-[8px] sm:text-[9px] text-emerald-600 font-bold uppercase tracking-[0.2em] mt-1 opacity-80">
Foundation
</span>
</div>

</a>

<!-- Desktop Navigation -->
<div class="hidden lg:flex items-center gap-1">

<?php foreach ($navLinks as $link): ?>

<a href="<?php echo $link['url']; ?>"
class="relative px-5 py-2 rounded-xl text-sm font-semibold transition-all duration-300 flex items-center gap-2
<?php echo $activeParent === $link['id'] ? 'text-emerald-700 bg-emerald-50' : 'text-slate-500 hover:text-emerald-600 hover:bg-slate-50'; ?>">

<i data-lucide="<?php echo $link['icon']; ?>" class="w-4 h-4
<?php echo $activeParent === $link['id'] ? 'text-emerald-600' : 'opacity-0 group-hover:opacity-100 transition-opacity'; ?>"></i>

<?php echo $link['name']; ?>

</a>

<?php endforeach; ?>

</div>

<!-- Mobile Menu + User Menu Buttons -->
<div class="flex items-center gap-2 lg:hidden">

    <?php if ($user || $isAdmin): ?>
    <div class="relative">
        <button id="mobileUserMenuToggle" type="button" aria-label="Open user menu" aria-expanded="false" class="p-2 rounded-xl text-slate-600 hover:bg-slate-100 hover:text-emerald-600 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-slate-900 flex items-center justify-center text-white font-bold shadow-lg shadow-slate-200">
            </div>
        </button>

        <div id="mobileUserMenu" class="hidden absolute right-0 mt-2 w-52 bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-slate-100 p-2 z-50">
            <div class="px-3 py-2 mb-2 bg-slate-50/50 rounded-2xl">
                <p class="text-[9px] font-semibold text-slate-400 uppercase tracking-widest">Logged in as</p>
                <p class="text-sm font-bold text-slate-900 truncate"><?php echo htmlspecialchars($user['email'] ?? 'Administrator'); ?></p>
            </div>

            <?php if ($isAdmin): ?>
            <a href="admin.php" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                <i data-lucide="shield" class="w-4 h-4"></i>
                Admin Dashboard
            </a>
            <?php endif; ?>

            <a href="profile.php" class="mt-2 flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                <i data-lucide="user-circle" class="w-4 h-4"></i>
                My Profile
            </a>

            <div class="my-2 border-t border-slate-100"></div>

            <a href="logout.php" class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-semibold text-red-500 hover:bg-red-50 transition-colors">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                Logout
            </a>
        </div>
    </div>
    <?php endif; ?>

    <button id="mobileMenuToggle" type="button" aria-label="Open mobile menu" aria-expanded="false" class="p-2 rounded-xl text-slate-600 hover:bg-slate-100 hover:text-emerald-600 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500">
        <i data-lucide="menu" class="w-6 h-6"></i>
    </button>
</div>

<!-- Action Area -->
<div class="hidden lg:flex items-center gap-4 shrink-0">

<a href="donate.php"
class="relative overflow-hidden bg-emerald-600 text-white px-7 py-3 rounded-2xl text-sm font-bold hover:bg-emerald-700 hover:shadow-xl hover:shadow-emerald-100 transition-all duration-300 active:scale-95 group">

<span class="relative z-10 flex items-center gap-2">
<i data-lucide="heart-handshake" class="w-4 h-4"></i>
Donate Now
</span>

<div class="absolute inset-0 bg-gradient-to-r from-emerald-600 to-emerald-500 translate-x-full group-hover:translate-x-0 transition-transform duration-500"></div>

</a>

<div class="h-8 w-px bg-slate-200 mx-1"></div>

<?php if ($user || $isAdmin): ?>

<div class="relative group">

<button class="flex items-center gap-3 p-1.5 pr-4 rounded-2xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100">

<div class="w-9 h-9 rounded-xl bg-slate-900 flex items-center justify-center text-white font-bold shadow-lg shadow-slate-200">
<?php echo strtoupper(substr(($user['name'] ?? 'A'), 0, 1)); ?>
</div>

<div class="text-left">
<p class="text-[11px] font-bold text-slate-900 leading-tight">
<?php echo htmlspecialchars($user['name'] ?? 'Admin'); ?>
</p>

<p class="text-[9px] text-emerald-600 font-bold uppercase tracking-tighter">
Account
</p>
</div>

<i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 group-hover:rotate-180 transition-transform duration-300"></i>

</button>

<!-- Dropdown -->
<div class="absolute right-0 top-full pt-3 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 translate-y-2 group-hover:translate-y-0 z-50">

<div class="w-56 bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-slate-100 p-2">

<div class="px-4 py-3 mb-2 bg-slate-50/50 rounded-2xl">

<p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
Logged in as
</p>

<p class="text-sm font-bold text-slate-900 truncate">
<?php echo htmlspecialchars($user['email'] ?? 'Administrator'); ?>
</p>

</div>

<?php if ($isAdmin): ?>

<a href="admin.php"
class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">

<i data-lucide="shield" class="w-4.5 h-4.5"></i>
Admin Dashboard

</a>

<?php endif; ?>

<?php if ($user): ?>

<a href="profile.php"
class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">

<i data-lucide="user-circle" class="w-4.5 h-4.5"></i>
My Profile

</a>

<?php endif; ?>

<div class="my-1 border-t border-slate-50"></div>

<a href="logout.php"
class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-red-500 hover:bg-red-50 transition-colors">

<i data-lucide="log-out" class="w-4.5 h-4.5"></i>
Logout

</a>

</div>
</div>
</div>

<?php else: ?>

<div class="flex items-center gap-2">

<a href="login.php"
class="px-5 py-2 text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">
Login
</a>

<a href="signup.php"
class="px-6 py-2.5 bg-slate-900 text-white text-sm font-bold rounded-2xl hover:bg-emerald-600 hover:shadow-xl hover:shadow-emerald-100 transition-all active:scale-95">
Signup
</a>

</div>

<?php endif; ?>

</div>

</div>

<!-- Mobile Menu -->
<div id="mobileMenu" class="lg:hidden hidden absolute inset-x-0 top-full bg-white border-b border-slate-100 shadow-xl z-50">
    <div class="max-w-7xl mx-auto px-3 py-3 space-y-2">
        <div class="flex items-center justify-between">
            <span class="text-sm font-semibold text-slate-700">Menu</span>
            <button id="mobileMenuClose" type="button" aria-label="Close mobile menu" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-emerald-600 transition-colors focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="grid gap-2">
            <?php foreach ($navLinks as $link): ?>
            <a href="<?php echo $link['url']; ?>"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-colors <?php echo $activeParent === $link['id'] ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50 hover:text-emerald-700'; ?>">
               <i data-lucide="<?php echo $link['icon']; ?>" class="w-4 h-4 <?php echo $activeParent === $link['id'] ? 'text-emerald-600' : ''; ?>"></i>
               <?php echo $link['name']; ?>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="pt-3 border-t border-slate-100">
            <a href="donate.php" class="block text-center w-full bg-emerald-600 text-white px-3 py-2.5 rounded-2xl font-bold text-sm hover:bg-emerald-700 transition-colors">
                Donate Now
            </a>
            <?php if ($user || $isAdmin): ?>
            <a href="profile.php" class="mt-2 block text-center w-full bg-slate-900 text-white px-3 py-2.5 rounded-2xl font-bold text-sm hover:bg-slate-800 transition-colors">
                My Account
            </a>
            <a href="logout.php" class="mt-2 block text-center w-full text-red-600 font-semibold hover:text-red-700">
                Logout
            </a>
            <?php else: ?>
            <div class="mt-2 grid gap-2">
                <a href="login.php" class="block text-center w-full px-3 py-2.5 rounded-2xl border border-slate-200 text-slate-700 font-semibold hover:bg-slate-50 transition-colors">
                    Login
                </a>
                <a href="signup.php" class="block text-center w-full px-3 py-2.5 rounded-2xl bg-slate-900 text-white font-bold hover:bg-emerald-600 transition-colors">
                    Signup
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</nav>

<script>

// Navbar shadow on scroll
window.addEventListener('scroll', () => {

const navbar = document.getElementById('navbar');

if (window.scrollY > 20) {

navbar.classList.add('shadow-xl','shadow-slate-200/50');
navbar.classList.replace('bg-white/80','bg-white/95');

} else {

navbar.classList.remove('shadow-xl','shadow-slate-200/50');
navbar.classList.replace('bg-white/95','bg-white/80');

}

});

// Initialize Lucide Icons
lucide.createIcons();

// Mobile menu toggle
const mobileMenu = document.getElementById('mobileMenu');
const mobileMenuToggle = document.getElementById('mobileMenuToggle');
const mobileMenuClose = document.getElementById('mobileMenuClose');

// Mobile user menu toggle
const mobileUserMenu = document.getElementById('mobileUserMenu');
const mobileUserMenuToggle = document.getElementById('mobileUserMenuToggle');

function setMobileMenu(open) {
    if (!mobileMenu || !mobileMenuToggle) return;
    mobileMenu.classList.toggle('hidden', !open);
    mobileMenuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
}

function setMobileUserMenu(open) {
    if (!mobileUserMenu || !mobileUserMenuToggle) return;
    mobileUserMenu.classList.toggle('hidden', !open);
    mobileUserMenuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
}

mobileMenuToggle?.addEventListener('click', () => setMobileMenu(true));
mobileMenuClose?.addEventListener('click', () => setMobileMenu(false));
mobileUserMenuToggle?.addEventListener('click', (event) => {
    event.stopPropagation();
    const open = mobileUserMenu && mobileUserMenu.classList.contains('hidden');
    setMobileUserMenu(open);
});

window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
        setMobileMenu(false);
        setMobileUserMenu(false);
    }
});

document.addEventListener('click', (event) => {
    const target = event.target;

    if (!mobileMenu || !mobileMenuToggle) return;
    if (!mobileMenu.classList.contains('hidden') && !mobileMenu.contains(target) && !mobileMenuToggle.contains(target)) {
        setMobileMenu(false);
    }

    if (mobileUserMenu && mobileUserMenuToggle && !mobileUserMenu.classList.contains('hidden') && !mobileUserMenu.contains(target) && !mobileUserMenuToggle.contains(target)) {
        setMobileUserMenu(false);
    }
});

</script>

