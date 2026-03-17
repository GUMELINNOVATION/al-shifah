<?php
// campaign-detail.php
require_once 'includes/data.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;
$campaign = getCampaignById($id, $CAMPAIGNS);

if (!$campaign) {
    header("Location: campaigns.php");
    exit;
}

$pageTitle = $campaign['title'];
$progress = ($campaign['currentAmount'] / $campaign['goalAmount']) * 100;

include_once 'includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 animate-in fade-in slide-in-from-bottom duration-500">
    <a href="campaigns.php" class="flex items-center gap-2 text-slate-500 hover:text-emerald-600 font-bold transition-colors mb-8">
        <i data-lucide="arrow-left" class="w-5 h-5"></i> Back to Campaigns
    </a>

    <div class="grid lg:grid-cols-3 gap-12">
        <div class="lg:col-span-2 space-y-12">
            <div class="space-y-6">
                <div class="flex items-center justify-between relative">
                    <span class="px-4 py-1.5 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded-full uppercase tracking-widest">
                        <?php echo $campaign['category']; ?>
                    </span>
                    
                    <div class="relative group">
                        <button id="share-button" class="p-3 text-slate-500 hover:text-emerald-600 bg-white border border-slate-200 rounded-full transition-all shadow-sm hover:shadow-md">
                            <i data-lucide="share-2" class="w-5 h-5"></i>
                        </button>

                        <div id="share-menu" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-2xl border border-slate-100 p-2 z-50 animate-in fade-in zoom-in duration-200 origin-top-right">
                            <div class="text-[10px] font-bold text-slate-400 px-3 py-2 uppercase tracking-widest">Share this cause</div>
                            <a href="#" class="share-link flex items-center gap-3 px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-50 transition-colors" data-platform="twitter">
                                <i data-lucide="twitter" class="w-5 h-5 text-emerald-600"></i>
                                <span class="text-sm font-medium">Twitter</span>
                            </a>
                            <a href="#" class="share-link flex items-center gap-3 px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-50 transition-colors" data-platform="facebook">
                                <i data-lucide="facebook" class="w-5 h-5 text-emerald-600"></i>
                                <span class="text-sm font-medium">Facebook</span>
                            </a>
                            <a href="#" class="share-link flex items-center gap-3 px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-50 transition-colors" data-platform="linkedin">
                                <i data-lucide="linkedin" class="w-5 h-5 text-emerald-600"></i>
                                <span class="text-sm font-medium">LinkedIn</span>
                            </a>
                            <div class="h-px bg-slate-100 my-1"></div>
                            <button id="copy-link-button" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-50 transition-colors">
                                <i data-lucide="copy" class="w-4.5 h-4.5"></i>
                                <span class="text-sm font-medium">Copy Link</span>
                            </button>
                        </div>
                    </div>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-slate-900 leading-tight">
                    <?php echo $campaign['title']; ?>
                </h1>
                <div class="aspect-video w-full rounded-[40px] overflow-hidden shadow-2xl">
                    <img src="<?php echo $campaign['image']; ?>" alt="<?php echo $campaign['title']; ?>" class="w-full h-full object-cover" />
                </div>
            </div>

            <div class="prose prose-emerald max-w-none">
                <h2 class="text-2xl font-bold text-slate-900 mb-6">About this program</h2>
                <div class="text-lg text-slate-600 leading-relaxed whitespace-pre-line">
                    <?php echo $campaign['longDescription']; ?>
                </div>
            </div>

            <div class="space-y-8">
                <h2 class="text-2xl font-bold text-slate-900">Program Gallery</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    <?php foreach ($campaign['gallery'] as $idx => $img): ?>
                        <div class="aspect-square rounded-[2rem] overflow-hidden hover:scale-105 transition-transform duration-300 shadow-md">
                            <img src="<?php echo $img; ?>" alt="Gallery <?php echo $idx; ?>" class="w-full h-full object-cover" />
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bg-slate-50 rounded-[40px] p-10 border border-slate-100 space-y-8 text-center md:text-left">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-2">Help Spread the Word</h3>
                        <p class="text-slate-600">Your voice is just as powerful as your donation.</p>
                    </div>
                    <div class="flex justify-center gap-4">
                        <a href="#" class="share-link flex items-center justify-center w-14 h-14 rounded-2xl text-white bg-[#1DA1F2] hover:bg-[#1a91da] transition-all shadow-lg hover:-translate-y-1" data-platform="twitter">
                            <i data-lucide="twitter" class="w-6 h-6"></i>
                        </a>
                        <a href="#" class="share-link flex items-center justify-center w-14 h-14 rounded-2xl text-white bg-[#4267B2] hover:bg-[#365899] transition-all shadow-lg hover:-translate-y-1" data-platform="facebook">
                            <i data-lucide="facebook" class="w-6 h-6"></i>
                        </a>
                        <a href="#" class="share-link flex items-center justify-center w-14 h-14 rounded-2xl text-white bg-[#0077b5] hover:bg-[#005e8d] transition-all shadow-lg hover:-translate-y-1" data-platform="linkedin">
                            <i data-lucide="linkedin" class="w-6 h-6"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="sticky top-24 bg-white rounded-[40px] p-8 shadow-2xl shadow-slate-200/50 border border-slate-100 space-y-8">
                <div class="space-y-5">
                    <div class="flex justify-between items-end">
                        <div class="text-4xl font-bold text-slate-900">
                            ₦<?php echo number_format($campaign['currentAmount']); ?>
                        </div>
                        <div class="text-slate-500 font-bold uppercase tracking-widest text-[10px]">
                            Goal: ₦<?php echo number_format($campaign['goalAmount']); ?>
                        </div>
                    </div>
                    <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                        <div class="bg-emerald-600 h-full rounded-full transition-all duration-1000 ease-out" style="width: <?php echo $progress; ?>%"></div>
                    </div>
                    <div class="flex justify-between text-xs font-bold text-emerald-600">
                        <span><?php echo number_format($progress, 1); ?>% Funded</span>
                        <span class="text-slate-400">Target Reached Soon</span>
                    </div>
                </div>

                <a href="donate.php?campaign=<?php echo $campaign['id']; ?>" class="w-full bg-emerald-600 text-white py-5 rounded-2xl font-bold text-lg shadow-xl shadow-emerald-100 hover:bg-emerald-700 transition-all flex items-center justify-center gap-2 transform active:scale-95">
                    <i data-lucide="heart" class="w-5 h-5 fill-white"></i> Support Now
                </a>

                <div class="space-y-6 pt-8 border-t border-slate-100">
                    <h3 class="font-bold text-slate-900 uppercase tracking-[0.2em] text-[10px]">Constitutional Commitments</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3 text-slate-600 text-sm">
                            <i data-lucide="check-circle-2" class="text-emerald-500 flex-shrink-0 w-5 h-5"></i>
                            <span>Article 13: Funds applied solely to humanitarian aims.</span>
                        </li>
                        <li class="flex items-start gap-3 text-slate-600 text-sm">
                            <i data-lucide="check-circle-2" class="text-emerald-500 flex-shrink-0 w-5 h-5"></i>
                            <span>Article 9: All disbursements require formal approval.</span>
                        </li>
                        <li class="flex items-start gap-3 text-slate-600 text-sm">
                            <i data-lucide="check-circle-2" class="text-emerald-500 flex-shrink-0 w-5 h-5"></i>
                            <span>Independent auditing for full accountability.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const shareButton = document.getElementById('share-button');
    const shareMenu = document.getElementById('share-menu');
    const copyLinkButton = document.getElementById('copy-link-button');

    shareButton.addEventListener('click', (e) => {
        e.stopPropagation();
        shareMenu.classList.toggle('hidden');
    });

    document.addEventListener('click', () => {
        shareMenu.classList.add('hidden');
    });

    shareMenu.addEventListener('click', (e) => {
        e.stopPropagation();
    });

    // Share logic
    const shareUrl = window.location.href;
    const shareText = "Help Al-Shifah Foundation: <?php echo $campaign['title']; ?>";

    document.querySelectorAll('.share-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const platform = link.getAttribute('data-platform');
            let url = '';
            
            if (platform === 'twitter') {
                url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}&url=${encodeURIComponent(shareUrl)}`;
            } else if (platform === 'facebook') {
                url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`;
            } else if (platform === 'linkedin') {
                url = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareUrl)}`;
            }
            
            window.open(url, '_blank', 'width=600,height=400');
        });
    });

    copyLinkButton.addEventListener('click', () => {
        navigator.clipboard.writeText(shareUrl).then(() => {
            const span = copyLinkButton.querySelector('span');
            const originalText = span.textContent;
            span.textContent = 'Link Copied!';
            copyLinkButton.querySelector('i').setAttribute('data-lucide', 'check');
            lucide.createIcons();
            
            setTimeout(() => {
                span.textContent = originalText;
                copyLinkButton.querySelector('i').setAttribute('data-lucide', 'copy');
                lucide.createIcons();
            }, 2000);
        });
    });
</script>

<?php include_once 'includes/footer.php'; ?>
