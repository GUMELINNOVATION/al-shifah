<?php
// about.php — Fully Dynamic
require_once 'includes/data.php';

$pageTitle = "About Us";
$CB = $CONTENT_BLOCKS;
$hero = $HERO_CONFIG['about'] ?? [];

include_once 'includes/header.php';
?>

<div class="pb-20">
    <!-- ══════════════ HERO BANNER ══════════════ -->
    <?php if (!empty($hero['image_url'])): ?>
    <div class="relative h-[45vh] overflow-hidden flex items-center">
        <img src="<?php echo htmlspecialchars($hero['image_url']); ?>"
             alt="About Hero"
             class="absolute inset-0 w-full h-full object-cover"
             style="filter: brightness(<?php echo 1 - floatval($hero['overlay_opacity'] ?? '0.55'); ?>);" />
        <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white">
            <?php if (!empty($hero['badge_text'])): ?>
            <span class="inline-block px-4 py-1.5 rounded-full bg-emerald-600/30 border border-emerald-400/50 text-emerald-100 text-xs font-bold uppercase tracking-widest mb-4">
                <?php echo htmlspecialchars($hero['badge_text']); ?>
            </span>
            <?php endif; ?>
            <h1 class="text-4xl md:text-6xl font-black leading-tight max-w-3xl"><?php echo htmlspecialchars($hero['title'] ?? ''); ?></h1>
        </div>
    </div>
    <?php endif; ?>

    <!-- ══════════════ INTRO & PREAMBLE ══════════════ -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 mb-24">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="animate-in fade-in slide-in-from-left duration-700">
                <?php
                $badge = htmlspecialchars($CB['about']['intro_badge'] ?? 'Official Constitution: Article 1');
                $heading = htmlspecialchars($CB['about']['intro_heading'] ?? '');
                $quote   = htmlspecialchars($CB['about']['intro_quote'] ?? '');
                $para    = htmlspecialchars($CB['about']['intro_paragraph'] ?? '');
                ?>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold uppercase tracking-wider mb-6 border border-emerald-100">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> <?php echo $badge; ?>
                </div>
                <h2 class="text-4xl md:text-5xl font-bold text-slate-900 mb-8 leading-tight"><?php echo $heading; ?></h2>
                <?php if (!empty($quote)): ?>
                <p class="text-lg text-slate-600 leading-relaxed mb-6 italic border-l-4 border-emerald-100 pl-6"><?php echo $quote; ?></p>
                <?php endif; ?>
                <p class="text-lg text-slate-600 leading-relaxed"><?php echo $para; ?></p>
            </div>
            <div class="relative">
                <div class="rounded-[40px] overflow-hidden shadow-2xl relative aspect-[4/5]">
                    <img src="<?php echo htmlspecialchars($hero['image_url'] ?? 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&q=80&w=600'); ?>"
                         class="w-full h-full object-cover" alt="Al-Shifah Field Work" />
                </div>
                <div class="absolute -bottom-8 -left-8 bg-emerald-600 text-white p-8 rounded-3xl shadow-xl z-20 max-w-[240px]">
                    <div class="text-sm font-bold opacity-80 uppercase tracking-widest mb-2"><?php echo htmlspecialchars($CB['about']['hq_label'] ?? 'Headquarters'); ?></div>
                    <div class="text-xs font-medium leading-relaxed"><?php echo htmlspecialchars($OFFICIAL_INFO['address']); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════ AIMS & OBJECTIVES ══════════════ -->
    <section id="aims" class="bg-slate-900 py-24 text-white overflow-hidden relative">
        <div class="absolute top-0 right-0 w-1/3 h-full bg-emerald-600/10 skew-x-12 transform translate-x-20"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-emerald-400 font-bold mb-4 uppercase tracking-widest text-sm">Article 3</h2>
                <h3 class="text-3xl md:text-4xl font-bold mb-6">Our Aims and Objectives</h3>
                <p class="text-slate-400 max-w-2xl mx-auto">The core pillars of our association as established in our official governing document.</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($objectives as $i => $obj): ?>
                    <div class="bg-white/5 border border-white/10 p-8 rounded-[32px] hover:bg-white/10 transition-all group">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center mb-6 font-bold text-xl group-hover:scale-110 transition-transform">
                            <?php echo $i + 1; ?>
                        </div>
                        <h4 class="text-xl font-bold mb-4 group-hover:text-emerald-400 transition-colors"><?php echo htmlspecialchars($obj['title']); ?></h4>
                        <p class="text-slate-400 text-sm leading-relaxed"><?php echo htmlspecialchars($obj['desc']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══════════════ GOVERNING BODY & TEAM ══════════════ -->
    <section id="team" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <?php
        $govHead  = htmlspecialchars($CB['about']['governance_heading'] ?? 'Governing Body');
        $govBody  = htmlspecialchars($CB['about']['governance_body'] ?? '');
        $bullet1  = htmlspecialchars($CB['about']['bullet_1'] ?? '');
        $bullet2  = htmlspecialchars($CB['about']['bullet_2'] ?? '');
        $bullet3  = htmlspecialchars($CB['about']['bullet_3'] ?? '');
        ?>
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4"><?php echo $govHead; ?></h2>
            <p class="text-slate-500 max-w-2xl mx-auto leading-relaxed"><?php echo $govBody; ?></p>
        </div>

        <!-- Team Cards -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-16">
            <?php foreach ($TEAM_MEMBERS as $member): ?>
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl hover:shadow-2xl hover:-translate-y-2 transition-all overflow-hidden group">
                    <?php if (!empty($member['photo_url'])): ?>
                        <div class="h-52 overflow-hidden">
                            <img src="<?php echo htmlspecialchars($member['photo_url']); ?>"
                                 alt="<?php echo htmlspecialchars($member['name']); ?>"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                        </div>
                    <?php else: ?>
                        <div class="h-36 bg-gradient-to-br from-emerald-50 to-emerald-100 flex items-center justify-center">
                            <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center text-3xl font-black text-emerald-600 shadow-lg">
                                <?php echo strtoupper(substr($member['name'], 0, 1)); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="p-7">
                        <div class="text-xs font-black text-emerald-600 uppercase tracking-widest mb-2"><?php echo htmlspecialchars($member['role']); ?></div>
                        <div class="text-xl font-black text-slate-900 mb-3"><?php echo htmlspecialchars($member['name']); ?></div>
                        <?php if (!empty($member['bio'])): ?>
                            <p class="text-sm text-slate-500 leading-relaxed"><?php echo htmlspecialchars($member['bio']); ?></p>
                        <?php endif; ?>
                        <span class="inline-block mt-3 px-3 py-1 bg-slate-50 text-slate-500 rounded-full text-[10px] font-black uppercase tracking-widest"><?php echo $member['type']; ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Governance Bullets -->
        <div class="bg-slate-50 rounded-[3rem] p-12 border border-slate-100">
            <ul class="space-y-5">
                <?php foreach ([$bullet1, $bullet2, $bullet3] as $b): if (empty($b)) continue; ?>
                    <li class="flex items-center gap-4 text-slate-600 text-lg">
                        <i data-lucide="check-circle-2" class="text-emerald-500 w-6 h-6 flex-shrink-0"></i>
                        <span><?php echo $b; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
</div>

<?php include_once 'includes/footer.php'; ?>
