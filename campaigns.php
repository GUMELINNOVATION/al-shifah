<?php
// campaigns.php
require_once 'includes/data.php';

$pageTitle = "Active Support Campaigns";

include_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 py-16 animate-in fade-in duration-500">
    <h1 class="text-4xl font-bold text-slate-900 mb-4">Active Support Campaigns</h1>
    <p class="text-slate-500 mb-12 max-w-2xl text-lg">
        Each campaign addresses a specific, urgent humanitarian need in our community. Join Al-Shifah in making a lasting difference.
    </p>
    
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
        <?php foreach ($CAMPAIGNS as $c): 
            $progress = ($c['currentAmount'] / $c['goalAmount']) * 100;
        ?>
            <div 
                class="group bg-white rounded-[40px] overflow-hidden shadow-sm border border-slate-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 cursor-pointer"
                onclick="window.location.href='campaign-detail.php?id=<?php echo $c['id']; ?>'"
            >
                <div class="relative h-64 overflow-hidden">
                    <img src="<?php echo $c['image']; ?>" alt="<?php echo $c['title']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" />
                    <div class="absolute top-6 left-6 bg-white/90 backdrop-blur px-4 py-1.5 rounded-2xl text-xs font-bold text-emerald-600 shadow-sm uppercase tracking-wider">
                        <?php echo $c['category']; ?>
                    </div>
                </div>
                <div class="p-8">
                    <h3 class="font-bold text-2xl mb-4 text-slate-900 leading-tight"><?php echo $c['title']; ?></h3>
                    <p class="text-slate-500 text-sm mb-8 leading-relaxed line-clamp-2"><?php echo $c['description']; ?></p>
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs font-bold uppercase tracking-widest">
                                <span class="text-emerald-600">₦<?php echo number_format($c['currentAmount']); ?> raised</span>
                                <span class="text-slate-400">Target: ₦<?php echo number_format($c['goalAmount']); ?></span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div 
                                    class="bg-emerald-600 h-full rounded-full" 
                                    style="width: <?php echo $progress; ?>%" 
                                ></div>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <a 
                                href="campaign-detail.php?id=<?php echo $c['id']; ?>"
                                class="flex-1 bg-slate-900 text-white py-4 rounded-2xl font-bold text-sm hover:bg-slate-800 transition-colors text-center"
                                onclick="event.stopPropagation();"
                            >
                                View Details
                            </a>
                            <a 
                                href="donate.php?campaign=<?php echo $c['id']; ?>"
                                class="px-5 py-4 rounded-2xl bg-emerald-50 text-emerald-600 font-bold hover:bg-emerald-100 transition-colors flex items-center justify-center"
                                onclick="event.stopPropagation();"
                            >
                                <i data-lucide="heart" class="w-5 h-5 fill-emerald-600"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
