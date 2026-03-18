<?php
// index.php — Fully Dynamic Homepage
require_once 'includes/data.php';

$pageTitle = "Home";

$totalRaised = array_reduce($CAMPAIGNS, fn($acc, $c) => $acc + $c['currentAmount'], 0);
$totalGoal   = array_reduce($CAMPAIGNS, fn($acc, $c) => $acc + $c['goalAmount'], 0);
$globalProgress = $totalGoal > 0 ? ($totalRaised / $totalGoal) * 100 : 0;

$hero = $HERO_CONFIG['home'] ?? [];
$CB   = $CONTENT_BLOCKS; // shorthand

include_once 'includes/header.php';
?>

<div class="pb-20">
<?php foreach ($HOME_SECTIONS as $section):
    if (!$section['is_visible']) continue;
    $key = $section['section_key'];
?>

<?php if ($key === 'hero'): ?>
    <!-- ══════════════ HERO SECTION ══════════════ -->
    <?php
    $heroType    = $hero['hero_type'] ?? 'image';
    $overlayOp   = $hero['overlay_opacity'] ?? '0.6';
    $badgeText   = htmlspecialchars($hero['badge_text'] ?? 'Official Al-Shifah Charity Foundation');
    $heroTitle   = $hero['title'] ?? 'Empowering Communities, Changing Lives.';
    $heroSub     = $hero['subtitle'] ?? '';
    $btnPrimTxt  = htmlspecialchars($hero['btn_primary_text'] ?? 'Donate Now');
    $btnPrimUrl  = htmlspecialchars($hero['btn_primary_url'] ?? 'donate.php');
    $btnSecTxt   = htmlspecialchars($hero['btn_secondary_text'] ?? 'Our Campaigns');
    $btnSecUrl   = htmlspecialchars($hero['btn_secondary_url'] ?? 'campaigns.php');
    $slides      = $hero['slides'] ?? [];
    $videoUrl    = $hero['video_url'] ?? '';
    ?>
    <section class="relative min-h-[70vh] md:min-h-[80vh] lg:h-[90vh] flex items-center overflow-hidden" id="hero-section">

        <!-- Background Layer -->
        <div class="absolute inset-0 z-0" id="hero-bg">
            <?php if ($heroType === 'video' && !empty($videoUrl)): ?>
                <?php if (str_contains($videoUrl, 'youtube') || str_contains($videoUrl, 'youtu.be')): ?>
                    <?php
                    preg_match('/(?:v=|youtu\.be\/)([^&\s]+)/', $videoUrl, $m);
                    $ytId = $m[1] ?? '';
                    ?>
                    <iframe src="https://www.youtube.com/embed/<?php echo $ytId; ?>?autoplay=1&mute=1&loop=1&playlist=<?php echo $ytId; ?>&controls=0&showinfo=0&rel=0"
                        class="absolute inset-0 w-full h-full object-cover scale-110"
                        style="pointer-events:none;"
                        frameborder="0" allow="autoplay"></iframe>
                <?php else: ?>
                    <video autoplay muted loop playsinline class="w-full h-full object-cover">
                        <source src="<?php echo htmlspecialchars($videoUrl); ?>" type="video/mp4">
                    </video>
                <?php endif; ?>

            <?php elseif ($heroType === 'slideshow' && !empty($slides)): ?>
                <?php foreach ($slides as $i => $slide): ?>
                    <div class="hero-slide absolute inset-0 transition-opacity duration-1000 <?php echo $i === 0 ? 'opacity-100' : 'opacity-0'; ?>" data-slide="<?php echo $i; ?>">
                        <img src="<?php echo htmlspecialchars($slide['image_url']); ?>"
                             alt="<?php echo htmlspecialchars($slide['caption'] ?? ''); ?>"
                             class="w-full h-full object-cover"
                             style="filter: brightness(<?php echo 1 - floatval($overlayOp); ?>);" />
                    </div>
                <?php endforeach; ?>
                <!-- Slide Dots -->
                <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 flex gap-2" id="slide-dots">
                    <?php foreach ($slides as $i => $slide): ?>
                        <button onclick="goToSlide(<?php echo $i; ?>)" class="slide-dot w-2.5 h-2.5 rounded-full transition-all <?php echo $i === 0 ? 'bg-white scale-125' : 'bg-white/40'; ?>"></button>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <img src="<?php echo htmlspecialchars($hero['image_url'] ?? ''); ?>"
                     alt="Hero"
                     class="w-full h-full object-cover"
                     style="filter: brightness(<?php echo 1 - floatval($overlayOp); ?>);" />
            <?php endif; ?>

            <!-- Gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>
        </div>

        <!-- Hero Content -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white w-full">
            <div class="max-w-3xl animate-in fade-in slide-in-from-left duration-700">
                <?php if (!empty($badgeText)): ?>
                <span class="inline-block px-4 py-1.5 rounded-full bg-emerald-600/30 border border-emerald-400/50 text-emerald-100 text-xs font-bold uppercase tracking-widest mb-8">
                    <?php echo $badgeText; ?>
                </span>
                <?php endif; ?>
                <h1 class="text-4xl sm:text-5xl md:text-7xl font-black leading-tight mb-6 tracking-tight">
                    <?php
                    // Bold first word in a span
                    $words = explode(' ', $heroTitle);
                    $last = array_pop($words);
                    echo implode(' ', $words) . ' <span class="text-emerald-400">' . htmlspecialchars($last) . '</span>';
                    ?>
                </h1>
                <p class="text-base sm:text-lg md:text-xl text-slate-200 mb-10 leading-relaxed max-w-2xl"><?php echo htmlspecialchars($heroSub); ?></p>
                <div class="flex flex-col sm:flex-row gap-4 sm:gap-5">
                    <a href="<?php echo $btnPrimUrl; ?>" class="bg-emerald-600 hover:bg-emerald-500 text-white px-8 py-4 sm:px-10 sm:py-5 rounded-2xl font-bold text-base sm:text-lg transition-all flex items-center justify-center gap-2 shadow-2xl shadow-emerald-900/40 hover:-translate-y-1">
                        <?php echo $btnPrimTxt; ?> <i data-lucide="arrow-right" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                    </a>
                    <a href="<?php echo $btnSecUrl; ?>" class="bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border border-white/30 px-8 py-4 sm:px-10 sm:py-5 rounded-2xl font-bold text-base sm:text-lg transition-all text-center hover:-translate-y-1">
                        <?php echo $btnSecTxt; ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php if ($heroType === 'slideshow' && count($slides) > 1): ?>
    <script>
    (function() {
        const slides = document.querySelectorAll('.hero-slide');
        const dots   = document.querySelectorAll('.slide-dot');
        let current  = 0, timer;

        window.goToSlide = function(n) {
            slides[current].classList.replace('opacity-100','opacity-0');
            dots[current].classList.replace('bg-white','bg-white/40');
            dots[current].classList.remove('scale-125');
            current = n % slides.length;
            slides[current].classList.replace('opacity-0','opacity-100');
            dots[current].classList.replace('bg-white/40','bg-white');
            dots[current].classList.add('scale-125');
        };

        function next() { goToSlide(current + 1); }
        timer = setInterval(next, 5000);

        document.getElementById('hero-section').addEventListener('mouseenter', () => clearInterval(timer));
        document.getElementById('hero-section').addEventListener('mouseleave', () => { timer = setInterval(next, 5000); });
    })();
    </script>
    <?php endif; ?>

<?php elseif ($key === 'tracker'): ?>
    <!-- ══════════════ IMPACT TRACKER ══════════════ -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 relative z-20">
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-emerald-900/20 p-8 md:p-12 border border-emerald-50">
            <div class="grid lg:grid-cols-3 gap-12 items-center">
                <div class="space-y-4">
                    <div class="flex items-center gap-3 text-emerald-600 font-bold uppercase tracking-widest text-xs">
                        <i data-lucide="trending-up" class="w-5 h-5"></i>
                        <?php echo htmlspecialchars($CB['tracker']['badge_label'] ?? 'Live Impact Tracker'); ?>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-900"><?php echo htmlspecialchars($CB['tracker']['heading'] ?? 'Collective Progress'); ?></h2>
                    <p class="text-slate-500"><?php echo htmlspecialchars($CB['tracker']['subheading'] ?? ''); ?></p>
                </div>
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex justify-between items-end">
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Raised</div>
                            <div class="text-4xl font-bold text-slate-900">₦<?php echo number_format($totalRaised); ?></div>
                        </div>
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Yearly Goal</div>
                            <div class="text-xl font-bold text-emerald-600">₦<?php echo number_format($totalGoal); ?></div>
                        </div>
                    </div>
                    <div class="relative h-4 bg-slate-100 rounded-full overflow-hidden">
                        <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-emerald-400 to-emerald-600 rounded-full transition-all duration-1000" style="width: <?php echo $globalProgress; ?>%"></div>
                    </div>
                    <div class="flex justify-between text-xs font-bold text-slate-400">
                        <span><?php echo number_format($globalProgress, 1); ?>% Completed</span>
                        <span>₦<?php echo number_format($totalGoal - $totalRaised); ?> remaining</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php elseif ($key === 'stats'): ?>
    <!-- ══════════════ STATS ══════════════ -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($IMPACT_STATS as $stat): ?>
                <div class="bg-white p-10 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 text-center hover:shadow-2xl transition-all hover:-translate-y-1">
                    <div class="inline-flex items-center justify-center w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl mb-5">
                        <i data-lucide="<?php echo $stat['icon']; ?>" class="w-7 h-7"></i>
                    </div>
                    <div class="text-4xl font-bold text-slate-900 mb-2"><?php echo $stat['value']; ?></div>
                    <div class="text-slate-500 text-sm font-semibold uppercase tracking-wider"><?php echo $stat['label']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

<?php elseif ($key === 'mission'): ?>
    <!-- ══════════════ MISSION SUMMARY ══════════════ -->
    <?php
    $mBadge = htmlspecialchars($CB['mission']['badge'] ?? 'Why We Exist');
    $mHead  = htmlspecialchars($CB['mission']['heading'] ?? '');
    $mPara  = htmlspecialchars($CB['mission']['paragraph'] ?? '');
    $mCta   = htmlspecialchars($CB['mission']['cta_text'] ?? 'Learn More');
    $mCtaU  = htmlspecialchars($CB['mission']['cta_url'] ?? 'about.php');
    $mImg   = htmlspecialchars($CB['mission']['image_url'] ?? '');
    $mQuote = $CB['mission']['quote'] ?? '';
    ?>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <h2 class="text-emerald-600 font-bold mb-4 uppercase tracking-widest text-sm"><?php echo $mBadge; ?></h2>
                <h3 class="text-3xl md:text-5xl font-bold text-slate-900 mb-8 leading-tight"><?php echo $mHead; ?></h3>
                <p class="text-lg text-slate-600 leading-relaxed mb-8"><?php echo $mPara; ?></p>
                <a href="<?php echo $mCtaU; ?>" class="group flex items-center gap-3 text-emerald-600 font-bold hover:gap-4 transition-all">
                    <?php echo $mCta; ?> <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
            <div class="relative">
                <div class="aspect-[4/3] rounded-[3rem] overflow-hidden shadow-2xl">
                    <img src="<?php echo $mImg; ?>" class="w-full h-full object-cover" alt="Mission" />
                </div>
                <?php if (!empty($mQuote)): ?>
                <div class="absolute -bottom-6 -right-6 bg-emerald-600 text-white p-8 rounded-3xl shadow-xl max-w-xs hidden md:block">
                    <p class="font-medium italic"><?php echo htmlspecialchars($mQuote); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php elseif ($key === 'campaigns'): ?>
    <!-- ══════════════ CAMPAIGNS ══════════════ -->
    <?php
    $cHead = htmlspecialchars($CB['campaigns']['heading'] ?? 'Active Support Programs');
    $cSub  = htmlspecialchars($CB['campaigns']['subheading'] ?? '');
    $cCta  = htmlspecialchars($CB['campaigns']['cta_text'] ?? 'View All');
    ?>
    <section class="bg-slate-50 py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-16">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4"><?php echo $cHead; ?></h2>
                    <p class="text-slate-500"><?php echo $cSub; ?></p>
                </div>
                <a href="campaigns.php" class="hidden sm:flex items-center gap-2 text-emerald-600 font-bold hover:underline">
                    <?php echo $cCta; ?> <i data-lucide="arrow-right" class="w-4.5 h-4.5"></i>
                </a>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <?php foreach ($CAMPAIGNS as $c): ?>
                    <div class="group bg-white rounded-[2.5rem] overflow-hidden shadow-sm border border-slate-100 hover:shadow-2xl transition-all duration-300">
                        <div class="relative h-56 overflow-hidden">
                            <img src="<?php echo $c['image']; ?>" alt="<?php echo htmlspecialchars($c['title']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                            <div class="absolute top-5 left-5 bg-white/90 backdrop-blur px-3 py-1 rounded-xl text-[10px] font-bold text-emerald-600 uppercase tracking-widest">
                                <?php echo $c['category']; ?>
                            </div>
                        </div>
                        <div class="p-8">
                            <h3 class="font-bold text-xl mb-4 text-slate-900 group-hover:text-emerald-600 transition-colors"><?php echo htmlspecialchars($c['title']); ?></h3>
                            <div class="space-y-6">
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="bg-emerald-600 h-full rounded-full" style="width: <?php echo ($c['currentAmount'] / max($c['goalAmount'], 1)) * 100; ?>%"></div>
                                </div>
                                <div class="flex justify-between text-xs font-bold">
                                    <span class="text-emerald-600">₦<?php echo number_format($c['currentAmount']); ?> Raised</span>
                                    <span class="text-slate-400">₦<?php echo number_format($c['goalAmount']); ?> Goal</span>
                                </div>
                                <a href="campaign-detail.php?id=<?php echo $c['id']; ?>" class="w-full block text-center bg-slate-900 text-white py-4 rounded-2xl font-bold hover:bg-emerald-600 transition-colors shadow-lg active:scale-95">
                                    Support Project
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<?php elseif ($key === 'gallery'): ?>
    <!-- ══════════════ GALLERY ══════════════ -->
    <?php
    $gBadge = htmlspecialchars($CB['gallery']['badge'] ?? 'Our Visual Impact');
    $gHead  = htmlspecialchars($CB['gallery']['heading'] ?? 'Field Update Gallery');
    $gSub   = htmlspecialchars($CB['gallery']['subheading'] ?? '');
    ?>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="text-center mb-16">
            <div class="inline-flex items-center gap-2 text-emerald-600 font-bold uppercase tracking-[0.2em] text-xs mb-4">
                <i data-lucide="image" class="w-4 h-4"></i> <?php echo $gBadge; ?>
            </div>
            <h2 class="text-3xl md:text-5xl font-bold text-slate-900 mb-4"><?php echo $gHead; ?></h2>
            <p class="text-slate-500 max-w-2xl mx-auto text-lg"><?php echo $gSub; ?></p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <?php foreach ($GALLERY_ITEMS as $idx => $img): ?>
                <div class="group relative overflow-hidden rounded-[2rem] shadow-lg transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl <?php echo $idx === 0 ? 'md:col-span-2 md:row-span-2' : ($idx === 5 ? 'md:col-span-2' : ''); ?>">
                    <img src="<?php echo $img['image_url']; ?>" alt="<?php echo htmlspecialchars($img['title'] ?? ''); ?>" class="w-full h-full object-cover aspect-square transition-transform duration-700 group-hover:scale-110" />
                    <div class="absolute inset-0 bg-gradient-to-t from-emerald-900/80 via-emerald-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-8">
                        <div>
                            <div class="text-white font-bold text-lg mb-1"><?php echo htmlspecialchars($img['title'] ?? ''); ?></div>
                            <div class="text-emerald-300 text-xs font-semibold uppercase tracking-widest">Update <?php echo date('Y', strtotime($img['created_at'])); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

<?php elseif ($key === 'blog'): ?>
    <!-- ══════════════ BLOG ══════════════ -->
    <?php
    $bHead = htmlspecialchars($CB['blog']['heading'] ?? 'Latest Insights');
    $bSub  = htmlspecialchars($CB['blog']['subheading'] ?? '');
    $bCta  = htmlspecialchars($CB['blog']['cta_text'] ?? 'Read All Updates');
    ?>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6 border-b border-slate-100 pb-10">
            <div>
                <h2 class="text-3xl md:text-5xl font-bold text-slate-900 mb-4"><?php echo $bHead; ?></h2>
                <p class="text-lg text-slate-500"><?php echo $bSub; ?></p>
            </div>
            <a href="blog.php" class="bg-white border border-slate-200 text-emerald-600 font-bold px-8 py-4 rounded-2xl hover:bg-emerald-50 transition-all flex items-center gap-2 text-center">
                <?php echo $bCta; ?> <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </a>
        </div>
        <div class="grid md:grid-cols-3 gap-12">
            <?php foreach ($BLOG_POSTS as $post): ?>
                <article class="group cursor-pointer">
                    <a href="blog-detail.php?id=<?php echo $post['id']; ?>" class="block">
                        <div class="relative aspect-[16/10] rounded-[2.5rem] overflow-hidden mb-8 shadow-md">
                            <img src="<?php echo $post['image']; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                            <div class="absolute top-5 left-5">
                                <span class="bg-white/95 backdrop-blur px-4 py-1.5 rounded-full text-[10px] font-bold text-emerald-600 uppercase tracking-widest shadow-sm">
                                    <?php echo $post['category']; ?>
                                </span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i> <?php echo $post['date']; ?>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-900 group-hover:text-emerald-600 transition-colors leading-tight">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </h3>
                            <p class="text-slate-500 leading-relaxed line-clamp-2"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                            <div class="pt-2 text-emerald-600 font-bold flex items-center gap-1 group-hover:gap-2 transition-all">
                                Read Full Story <i data-lucide="arrow-right" class="w-4.5 h-4.5"></i>
                            </div>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

<?php endif; ?>
<?php endforeach; ?>
</div>

<?php include_once 'includes/footer.php'; ?>
