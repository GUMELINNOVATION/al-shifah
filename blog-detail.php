<?php
// blog-detail.php
require_once 'includes/data.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;
$post = getBlogPostById($id, $BLOG_POSTS);

if (!$post) {
    header("Location: index.php#blog");
    exit;
}

$pageTitle = $post['title'];

include_once 'includes/header.php';
?>

<div class="max-w-4xl mx-auto px-4 py-12 animate-in fade-in slide-in-from-bottom duration-500">
    <a href="index.php#blog" class="flex items-center gap-2 text-slate-500 hover:text-emerald-600 font-bold transition-colors mb-10">
        <i data-lucide="arrow-left" class="w-5 h-5"></i> Back to Updates
    </a>

    <article class="space-y-10">
        <header class="space-y-6">
            <div class="flex items-center gap-4">
                <span class="px-4 py-1.5 bg-emerald-50 text-emerald-600 text-[10px] font-bold rounded-full uppercase tracking-widest border border-emerald-100">
                    <?php echo $post['category']; ?>
                </span>
                <div class="flex items-center gap-2 text-slate-400 text-xs font-bold uppercase tracking-wider">
                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i> <?php echo $post['date']; ?>
                </div>
            </div>
            
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 leading-tight tracking-tight">
                <?php echo $post['title']; ?>
            </h1>

            <div class="flex items-center justify-between py-6 border-y border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-black">
                        AS
                    </div>
                    <div>
                        <div class="text-sm font-bold text-slate-900">Al-Shifah Editorial</div>
                        <div class="text-xs text-slate-400">Governance & Communications</div>
                    </div>
                </div>
                
                <div class="relative group">
                    <button id="blog-share-button" class="p-3 text-slate-500 hover:text-emerald-600 bg-white border border-slate-100 rounded-2xl transition-all shadow-sm flex items-center gap-2 text-sm font-bold">
                        <i data-lucide="share-2" class="w-4.5 h-4.5"></i> Share
                    </button>
                    
                    <div id="blog-share-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-2xl border border-slate-100 p-2 z-50 animate-in fade-in zoom-in duration-200 origin-top-right">
                        <a href="#" class="share-link w-full flex items-center gap-3 px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-50 transition-colors text-sm font-medium" data-platform="twitter">
                            <i data-lucide="twitter" class="w-4.5 h-4.5 text-emerald-600"></i>
                            Twitter
                        </a>
                        <a href="#" class="share-link w-full flex items-center gap-3 px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-50 transition-colors text-sm font-medium" data-platform="facebook">
                            <i data-lucide="facebook" class="w-4.5 h-4.5 text-emerald-600"></i>
                            Facebook
                        </a>
                        <a href="#" class="share-link w-full flex items-center gap-3 px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-50 transition-colors text-sm font-medium" data-platform="linkedin">
                            <i data-lucide="linkedin" class="w-4.5 h-4.5 text-emerald-600"></i>
                            LinkedIn
                        </a>
                        <div class="h-px bg-slate-100 my-1"></div>
                        <button id="blog-copy-link" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-50 transition-colors text-sm font-medium">
                            <i data-lucide="copy" class="w-4.5 h-4.5"></i>
                            Copy Link
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <div class="aspect-[16/9] w-full rounded-[3.5rem] overflow-hidden shadow-2xl">
            <img src="<?php echo $post['image']; ?>" alt="<?php echo $post['title']; ?>" class="w-full h-full object-cover" />
        </div>

        <div class="prose prose-emerald max-w-none">
            <p class="text-xl font-bold text-slate-800 leading-relaxed mb-8 italic border-l-4 border-emerald-500 pl-6 bg-slate-50 py-4 rounded-r-2xl">
                <?php echo $post['excerpt']; ?>
            </p>
            <div class="text-lg text-slate-600 leading-relaxed whitespace-pre-line space-y-6">
                <?php echo $post['content']; ?>
            </div>
        </div>

        <footer class="pt-12 border-t border-slate-100">
            <div class="bg-slate-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-10">
                    <i data-lucide="tag" class="w-24 h-24"></i>
                </div>
                <div class="relative z-10 space-y-4">
                    <h4 class="text-2xl font-bold">Join the Conversation</h4>
                    <p class="text-slate-400 max-w-md">Our updates are driven by real community feedback. Share your thoughts or inquire about these programs.</p>
                    <div class="flex gap-4 pt-4 flex-wrap">
                        <a href="contact.php" class="bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-900/40 text-center">
                            Contact Editorial
                        </a>
                        <a href="donate.php" class="bg-white/10 text-white px-8 py-3 rounded-xl font-bold hover:bg-white/20 transition-all border border-white/10 text-center">
                            Support Our Mission
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </article>
</div>

<script>
    const shareButton = document.getElementById('blog-share-button');
    const shareMenu = document.getElementById('blog-share-menu');
    const copyLinkButton = document.getElementById('blog-copy-link');

    shareButton.addEventListener('click', (e) => {
        e.stopPropagation();
        shareMenu.classList.toggle('hidden');
    });

    document.addEventListener('click', () => {
        shareMenu.classList.add('hidden');
    });

    // Share logic
    const shareUrl = window.location.href;
    const shareText = "Insight from Al-Shifah Foundation: <?php echo $post['title']; ?>";

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
            const originalContent = copyLinkButton.innerHTML;
            copyLinkButton.innerHTML = '<i data-lucide="check" class="w-4.5 h-4.5 text-emerald-500"></i> Copied!';
            lucide.createIcons();
            
            setTimeout(() => {
                copyLinkButton.innerHTML = originalContent;
                lucide.createIcons();
            }, 2000);
        });
    });
</script>

<?php include_once 'includes/footer.php'; ?>
