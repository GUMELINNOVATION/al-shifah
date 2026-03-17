<?php
// includes/admin_tabs.php — All tab views for the admin panel
// Included by admin.php after $tab is set.

// Reusable upload widget helper
function uploadWidget(string $widgetId, string $fieldId, string $currentValue='', string $accept='image/*', string $label='Image'): void {
    $isImg = str_contains($accept, 'image');
    $isVid = str_contains($accept, 'video');
    $iDoc  = str_contains($accept, 'pdf');
    $hint  = $isImg ? 'JPG, PNG, GIF, WebP — up to 50MB' : ($isVid ? 'MP4, WebM, MOV — up to 50MB' : 'PDF, DOC, DOCX — up to 50MB');
    $url   = htmlspecialchars($currentValue);
    ?>
    <input type="hidden" id="<?php echo $fieldId; ?>" name="<?php echo $fieldId; ?>" value="<?php echo $url; ?>">
    <div id="<?php echo $widgetId; ?>" class="upload-widget" data-upload-widget data-field="<?php echo $fieldId; ?>">
      <div class="tabs">
        <button type="button" class="tab-btn active" data-panel="upload">Upload File</button>
        <button type="button" class="tab-btn" data-panel="url">Paste URL</button>
      </div>
      <div class="upload-panel">
        <div class="drop-zone">
          <svg width="22" height="22" fill="none" stroke="#9ca3af" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 6px;display:block;"><path d="M4 16l4-4 4 4M12 12l4-4 4 4M12 12V20"/><rect x="2" y="3" width="20" height="14" rx="2"/></svg>
          <span style="font-size:12px;color:var(--muted);">Drop file here or <strong>browse</strong></span><br>
          <span style="font-size:11px;color:#9ca3af;">Any file format (No active limits)</span>
          <input type="file" style="display:none;">
        </div>
      </div>
      <div class="url-panel" style="display:none;">
        <input type="text" class="form-input url-input" placeholder="https://..." value="<?php echo $url; ?>" style="margin-top:0;">
      </div>
      <div class="preview-area" style="display:<?php echo $currentValue?'flex':'none'; ?>;">
        <img src="<?php echo $isImg && $currentValue ? $url : ''; ?>" class="preview-thumb" style="display:<?php echo ($isImg && $currentValue) ? 'block' : 'none'; ?>;" alt="">
        <span class="preview-icon" style="font-size:20px;display:<?php echo (!$isImg || !$currentValue) ? 'block' : 'none'; ?>;"><?php echo $isVid?'🎬':($iDoc?'📄':'📎'); ?></span>
        <span class="preview-name"><?php echo $currentValue ? basename($currentValue) : ''; ?></span>
        <button type="button" onclick="clearUpload('<?php echo $fieldId; ?>','<?php echo $widgetId; ?>')" class="btn btn-sm btn-secondary" style="margin-left:auto;">✕</button>
      </div>
    </div>
    <?php
}

// Dashboard
if ($tab === 'dashboard'):
    $donations   = $pdo->query("SELECT * FROM donations ORDER BY created_at DESC LIMIT 8")->fetchAll();
    $recentMsgs  = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>
<div class="stat-grid">
  <?php
  $stats = [
    ['label'=>'Total Raised','value'=>'₦'.number_format($totalRaised),'icon'=>'banknote','color'=>'#d1fae5','ic'=>'var(--emerald)'],
    ['label'=>'Total Donors','value'=>$totalDonors,'icon'=>'users','color'=>'#dbeafe','ic'=>'#2563eb'],
    ['label'=>'Unread Messages','value'=>$unreadMsgs,'icon'=>'mail','color'=>'#fce7f3','ic'=>'#db2777'],
    ['label'=>'Active Campaigns','value'=>$activeCamps,'icon'=>'flag','color'=>'#fef3c7','ic'=>'#d97706'],
  ];
  foreach ($stats as $s): ?>
  <div class="stat-card">
    <div class="icon-wrap" style="background:<?php echo $s['color']; ?>;"><i data-lucide="<?php echo $s['icon']; ?>" style="width:17px;height:17px;color:<?php echo $s['ic']; ?>;"></i></div>
    <div class="label"><?php echo $s['label']; ?></div>
    <div class="value"><?php echo $s['value']; ?></div>
  </div>
  <?php endforeach; ?>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
  <div class="card">
    <div class="card-header"><h2>Recent Donations</h2></div>
    <table class="data-table">
      <thead><tr><th>Donor</th><th>Amount</th><th>Date</th></tr></thead>
      <tbody>
        <?php foreach ($donations as $d): ?>
        <tr><td><?php echo htmlspecialchars($d['name']??'—'); ?></td><td>₦<?php echo number_format($d['amount']); ?></td><td style="color:var(--muted);font-size:12px;"><?php echo date('M j',strtotime($d['created_at']??'now')); ?></td></tr>
        <?php endforeach; if(!$donations): ?><tr><td colspan="3" style="text-align:center;color:var(--muted);padding:20px;">No donations yet</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="card">
    <div class="card-header"><h2>Recent Messages</h2></div>
    <table class="data-table">
      <thead><tr><th>From</th><th>Subject</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($recentMsgs as $m): ?>
        <tr><td><?php echo htmlspecialchars($m['name']??'—'); ?></td><td style="font-size:12px;"><?php echo htmlspecialchars(substr($m['subject']??'',0,30)); ?></td><td><span class="badge-pill <?php echo $m['status']==='unread'?'badge-blue':'badge-green'; ?>"><?php echo $m['status']; ?></span></td></tr>
        <?php endforeach; if(!$recentMsgs): ?><tr><td colspan="3" style="text-align:center;color:var(--muted);padding:20px;">No messages</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php elseif ($tab === 'treasury'):
    $totalDonations = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM donations")->fetchColumn();
    $totalIncome = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM financial_data WHERE category='income'")->fetchColumn();
    $totalExp = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM financial_data WHERE category='expenditure'")->fetchColumn();
    $balance = ($totalDonations + $totalIncome) - $totalExp;
    $recentFinances = $pdo->query("SELECT * FROM financial_data ORDER BY id DESC LIMIT 5")->fetchAll();
?>
<div class="section-header">
  <div><p class="section-title">Treasury Management</p><p class="section-sub">High-level overview of organizational funds and recent allocations.</p></div>
  <a href="?tab=finance" class="btn btn-primary">Manage Records</a>
</div>
<div class="stat-grid" style="grid-template-columns:repeat(auto-fit,minmax(250px,1fr));">
  <div class="stat-card" style="border-left:4px solid #10b981;">
    <div class="label">Total Inflow (Donations + Income)</div>
    <div class="value" style="color:#15803d;">₦<?php echo number_format($totalDonations + $totalIncome); ?></div>
  </div>
  <div class="stat-card" style="border-left:4px solid #ef4444;">
    <div class="label">Total Expenditure</div>
    <div class="value" style="color:#b91c1c;">₦<?php echo number_format($totalExp); ?></div>
  </div>
  <div class="stat-card" style="border-left:4px solid #3b82f6;">
    <div class="label">Net Available Balance</div>
    <div class="value" style="color:#1d4ed8;">₦<?php echo number_format($balance); ?></div>
  </div>
</div>
<div class="card">
  <div class="card-header"><h2>Recent Financial Activity</h2><span class="sub">Latest processed transactions</span></div>
  <table class="data-table">
    <thead><tr><th>Year</th><th>Category</th><th>Amount</th><th>Notes</th></tr></thead>
    <tbody>
      <?php foreach ($recentFinances as $f): ?>
      <tr>
        <td><?php echo htmlspecialchars($f['fiscal_year']); ?></td>
        <td><span class="badge-pill <?php echo $f['category']==='income'?'badge-green':'badge-red'; ?>"><?php echo ucfirst($f['category']); ?></span></td>
        <td style="font-weight:600;">₦<?php echo number_format($f['amount']); ?></td>
        <td style="color:var(--muted);font-size:12px;"><?php echo htmlspecialchars(substr($f['usage_context']??'',0,60)); ?></td>
      </tr>
      <?php endforeach; if(!$recentFinances): ?><tr><td colspan="4" style="text-align:center;color:var(--muted);padding:20px;">No recent records</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php elseif ($tab === 'reports'):
    // Gather data for Charts & Tables
    // Monthly Donations
    $monthlyData = $pdo->query("SELECT DATE_FORMAT(donation_date, '%Y-%m') as month, SUM(amount) as total FROM donations GROUP BY month ORDER BY month ASC LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);
    $months = json_encode(array_column($monthlyData, 'month'));
    $totals = json_encode(array_column($monthlyData, 'total'));
    // Latest Donations
    $allDonations = $pdo->query("SELECT * FROM donations ORDER BY donation_date DESC, id DESC LIMIT 50")->fetchAll();
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="section-header">
  <div><p class="section-title">Data Reports</p><p class="section-sub">Analytics and exportable financial reports.</p></div>
  <div style="display:flex;gap:12px;">
      <button onclick="exportCSV()" class="btn btn-secondary" style="display:flex;align-items:center;gap:6px;background:#fff;border:1px solid var(--border);color:var(--text);"><i data-lucide="file-spreadsheet" style="width:16px;"></i> Export Excel/CSV</button>
      <button onclick="exportPDF()" class="btn btn-primary" style="display:flex;align-items:center;gap:6px;"><i data-lucide="download" style="width:16px;"></i> Export PDF</button>
  </div>
</div>

<div id="report-container" style="background:#fff;padding:32px;border-radius:16px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.02);border:1px solid rgba(0,0,0,0.05);">
  <div style="text-align:center;margin-bottom:30px;padding-bottom:20px;border-bottom:1px solid #e2e8f0;">
    <h1 style="margin:0;font-size:24px;color:#0f172a;">Al-Shifah Financial Report</h1>
    <p style="margin:4px 0 0;color:#64748b;font-size:14px;">Generated on <?php echo date('F j, Y'); ?></p>
  </div>

  <div style="margin-bottom:40px;">
    <h3 style="font-size:16px;color:#0f172a;margin-bottom:16px;">Monthly Donation Inflow</h3>
    <div style="height:300px;position:relative;">
      <canvas id="donationChart"></canvas>
    </div>
  </div>

  <div>
    <h3 style="font-size:16px;color:#0f172a;margin-bottom:16px;">Recent Donation Log (Last 50)</h3>
    <table class="data-table" id="export-table" style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;">
      <thead>
        <tr><th>Date</th><th>Donor Name</th><th>Amount (₦)</th><th>Identity</th></tr>
      </thead>
      <tbody>
        <?php foreach ($allDonations as $d): ?>
        <tr>
          <td><?php echo htmlspecialchars($d['donation_date']); ?></td>
          <td><?php echo htmlspecialchars($d['donor_name']); ?></td>
          <td style="font-family:monospace;font-size:13px;"><?php echo number_format($d['amount'], 2); ?></td>
          <td><?php echo $d['is_anonymous'] ? 'Anonymous' : 'Public'; ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('donationChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo $months; ?>,
            datasets: [{
                label: 'Donations (₦)',
                data: <?php echo $totals; ?>,
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                borderColor: '#10b981',
                borderWidth: 2,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });
});

function exportPDF() {
    const element = document.getElementById('report-container');
    const opt = {
      margin:       0.5,
      filename:     'al_shifah_report_<?php echo date("Y_m_d"); ?>.pdf',
      image:        { type: 'jpeg', quality: 0.98 },
      html2canvas:  { scale: 2 },
      jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(element).save();
}

function exportCSV() {
    let table = document.getElementById("export-table");
    let rows = Array.from(table.querySelectorAll("tr"));
    let csv = rows.map(row => {
        let cols = Array.from(row.querySelectorAll("th, td"));
        return cols.map(c => '"' + c.innerText.replace(/"/g, '""') + '"').join(",");
    }).join("\n");

    let blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
    let link = document.createElement("a");
    let url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", 'al_shifah_data_<?php echo date("Y_m_d"); ?>.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<?php elseif ($tab === 'finance'):
    $records = $pdo->query("SELECT * FROM financial_data ORDER BY fiscal_year DESC, id DESC")->fetchAll();
?>
<div class="section-header">
  <div><p class="section-title">Finance Records</p><p class="section-sub">Track income and expenditure by fiscal year.</p></div>
</div>
<div style="display:grid;grid-template-columns:1fr 340px;gap:16px;align-items:start;">
  <div class="card">
    <div class="card-header"><h2>All Records</h2></div>
    <table class="data-table">
      <thead><tr><th>Year</th><th>Category</th><th>Amount (₦)</th><th>Notes</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($records as $r): ?>
        <tr>
          <td><?php echo htmlspecialchars($r['fiscal_year']); ?></td>
          <td><span class="badge-pill <?php echo $r['category']==='income'?'badge-green':'badge-red'; ?>"><?php echo $r['category']; ?></span></td>
          <td>₦<?php echo number_format($r['amount']); ?></td>
          <td style="font-size:12px;color:var(--muted);"><?php echo htmlspecialchars(substr($r['usage_context']??'',0,40)); ?></td>
          <td>
            <form method="POST" onsubmit="return confirm('Delete?');">
              <input type="hidden" name="financial_action" value="delete">
              <input type="hidden" name="financial_id" value="<?php echo $r['id']; ?>">
              <button class="btn btn-sm btn-danger btn-icon"><i data-lucide="trash-2" style="width:13px;height:13px;"></i></button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="card">
    <div class="card-header"><h2>Add Record</h2></div>
    <div class="card-body">
      <form method="POST" class="space-y-3">
        <input type="hidden" name="financial_action" value="add">
        <div class="form-group"><label class="form-label">Fiscal Year</label><input type="text" name="fiscal_year" class="form-input" placeholder="e.g. 2024" required></div>
        <div class="form-group"><label class="form-label">Category</label>
          <select name="category" class="form-input form-select">
            <option value="income">Income / Donation</option>
            <option value="expenditure">Expenditure</option>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Amount (₦)</label><input type="number" name="amount" class="form-input" placeholder="0.00" step="0.01" required></div>
        <div class="form-group"><label class="form-label">Notes</label><textarea name="usage_context" class="form-input" rows="2" placeholder="Describe how this money was used or received"></textarea></div>
        <button class="btn btn-primary" style="width:100%;">Add Record</button>
      </form>
    </div>
  </div>
</div>

<?php elseif ($tab === 'messages'):
    $messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
?>
<div class="section-header">
  <div><p class="section-title">Messages</p><p class="section-sub">Contact form submissions from your website.</p></div>
</div>
<div class="card">
  <table class="data-table">
    <thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Message</th><th>Date</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($messages as $m): ?>
      <tr style="<?php echo $m['status']==='unread'?'font-weight:600;':''; ?>">
        <td><?php echo htmlspecialchars($m['name']); ?></td>
        <td style="font-size:12px;"><a href="mailto:<?php echo $m['email']; ?>" style="color:var(--emerald);"><?php echo htmlspecialchars($m['email']); ?></a></td>
        <td><?php echo htmlspecialchars($m['subject']); ?></td>
        <td style="font-size:12px;color:var(--muted);max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo htmlspecialchars($m['message']); ?></td>
        <td style="font-size:12px;color:var(--muted);"><?php echo isset($m['created_at'])?date('M j',strtotime($m['created_at'])):'—'; ?></td>
        <td><span class="badge-pill <?php echo $m['status']==='unread'?'badge-blue':'badge-green'; ?>"><?php echo $m['status']; ?></span></td>
        <td style="display:flex;gap:6px;">
          <?php if($m['status']==='unread'): ?>
          <form method="POST"><input type="hidden" name="message_action" value="read"><input type="hidden" name="message_id" value="<?php echo $m['id']; ?>"><button class="btn btn-sm btn-secondary btn-icon" title="Mark read"><i data-lucide="check" style="width:13px;height:13px;"></i></button></form>
          <?php endif; ?>
          <form method="POST" onsubmit="return confirm('Delete?');"><input type="hidden" name="message_action" value="delete"><input type="hidden" name="message_id" value="<?php echo $m['id']; ?>"><button class="btn btn-sm btn-danger btn-icon"><i data-lucide="trash-2" style="width:13px;height:13px;"></i></button></form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php elseif ($tab === 'campaigns'):
    $campaigns = $pdo->query("SELECT * FROM campaigns ORDER BY id DESC")->fetchAll();
    $editCamp  = null;
    if (isset($_GET['edit'])) $editCamp = $pdo->prepare("SELECT * FROM campaigns WHERE id=?") ? ($s=$pdo->prepare("SELECT * FROM campaigns WHERE id=?"))&&$s->execute([$_GET['edit']]) ? $s->fetch() : null : null;
?>
<div style="display:grid;grid-template-columns:1fr 380px;gap:16px;align-items:start;">
  <div class="card">
    <div class="card-header"><h2>All Campaigns</h2><a href="?tab=campaigns" class="btn btn-sm btn-primary"><i data-lucide="plus" style="width:13px;height:13px;"></i> New</a></div>
    <table class="data-table">
      <thead><tr><th>Campaign</th><th>Category</th><th>Goal</th><th>Raised</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($campaigns as $c):
          $raised = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM donations WHERE campaign_id=?");
          $raised->execute([$c['id']]); $r=$raised->fetchColumn();
        ?>
        <tr>
          <td>
            <?php if(!empty($c['image_url'])): ?><img src="<?php echo htmlspecialchars($c['image_url']); ?>" style="width:36px;height:36px;border-radius:6px;object-fit:cover;margin-right:8px;vertical-align:middle;"><?php endif; ?>
            <strong><?php echo htmlspecialchars($c['title']); ?></strong>
          </td>
          <td><span class="badge-pill badge-blue"><?php echo htmlspecialchars($c['category']); ?></span></td>
          <td>₦<?php echo number_format($c['goal_amount']); ?></td>
          <td>₦<?php echo number_format($r); ?></td>
          <td style="display:flex;gap:6px;">
            <a href="?tab=campaigns&edit=<?php echo $c['id']; ?>" class="btn btn-sm btn-secondary btn-icon"><i data-lucide="pencil" style="width:13px;height:13px;"></i></a>
            <form method="POST" onsubmit="return confirm('Delete campaign?');"><input type="hidden" name="campaign_action" value="delete"><input type="hidden" name="campaign_id" value="<?php echo $c['id']; ?>"><button class="btn btn-sm btn-danger btn-icon"><i data-lucide="trash-2" style="width:13px;height:13px;"></i></button></form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="card">
    <div class="card-header"><h2><?php echo $editCamp?'Edit Campaign':'New Campaign'; ?></h2></div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="campaign_action" value="save">
        <input type="hidden" name="campaign_id" value="<?php echo $editCamp['id']??''; ?>">
        <div class="form-group"><label class="form-label">Title</label><input type="text" name="title" class="form-input" value="<?php echo htmlspecialchars($editCamp['title']??''); ?>" required></div>
        <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-input" rows="3"><?php echo htmlspecialchars($editCamp['description']??''); ?></textarea></div>
        <div class="form-grid">
          <div class="form-group"><label class="form-label">Goal (₦)</label><input type="number" name="goal_amount" class="form-input" value="<?php echo $editCamp['goal_amount']??''; ?>" required></div>
          <div class="form-group"><label class="form-label">Category</label>
            <select name="category" class="form-input form-select">
              <?php foreach(['Health','Education','Water','Emergency','General'] as $cat): ?>
              <option <?php echo ($editCamp['category']??'')===$cat?'selected':''; ?>><?php echo $cat; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Campaign Image</label>
          <?php uploadWidget('camp-img','image_url',$editCamp['image_url']??'','image/*','Image'); ?>
        </div>
        <div class="form-group">
          <label class="form-label">Campaign Thumbnail (Optional)</label>
          <?php uploadWidget('camp-thumb','thumbnail_url',$editCamp['thumbnail_url']??'','image/*','Image'); ?>
        </div>
        <button class="btn btn-primary" style="width:100%;">Save Campaign</button>
      </form>
    </div>
  </div>
</div>

<?php elseif ($tab === 'blogs'):
    $posts   = $pdo->query("SELECT * FROM blog_posts ORDER BY id DESC")->fetchAll();
    $editPost = null;
    if (isset($_GET['edit'])) { $s=$pdo->prepare("SELECT * FROM blog_posts WHERE id=?"); $s->execute([$_GET['edit']]); $editPost=$s->fetch(); }
?>
<div style="display:grid;grid-template-columns:1fr 380px;gap:16px;align-items:start;">
  <div class="card">
    <div class="card-header"><h2>Blog Posts</h2><a href="?tab=blogs" class="btn btn-sm btn-primary"><i data-lucide="plus" style="width:13px;height:13px;"></i> New Post</a></div>
    <table class="data-table">
      <thead><tr><th>Title</th><th>Category</th><th>Date</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($posts as $p): ?>
        <tr>
          <td><?php if(!empty($p['image_url'])): ?><img src="<?php echo htmlspecialchars($p['image_url']); ?>" style="width:32px;height:32px;border-radius:6px;object-fit:cover;margin-right:8px;vertical-align:middle;"><?php endif; ?><?php echo htmlspecialchars($p['title']); ?></td>
          <td><span class="badge-pill badge-yellow"><?php echo htmlspecialchars($p['category']); ?></span></td>
          <td style="font-size:12px;color:var(--muted);"><?php echo $p['post_date']??''; ?></td>
          <td style="display:flex;gap:6px;">
            <a href="?tab=blogs&edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-secondary btn-icon"><i data-lucide="pencil" style="width:13px;height:13px;"></i></a>
            <form method="POST" onsubmit="return confirm('Delete?');"><input type="hidden" name="blog_action" value="delete"><input type="hidden" name="blog_id" value="<?php echo $p['id']; ?>"><button class="btn btn-sm btn-danger btn-icon"><i data-lucide="trash-2" style="width:13px;height:13px;"></i></button></form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="card">
    <div class="card-header"><h2><?php echo $editPost?'Edit Post':'New Post'; ?></h2></div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="blog_action" value="save">
        <input type="hidden" name="blog_id" value="<?php echo $editPost['id']??''; ?>">
        <div class="form-group"><label class="form-label">Title</label><input type="text" name="title" class="form-input" value="<?php echo htmlspecialchars($editPost['title']??''); ?>" required></div>
        <div class="form-group"><label class="form-label">Excerpt</label><textarea name="excerpt" class="form-input" rows="2"><?php echo htmlspecialchars($editPost['excerpt']??''); ?></textarea></div>
        <div class="form-group"><label class="form-label">Full Content</label><textarea name="content" class="form-input" rows="4"><?php echo htmlspecialchars($editPost['content']??''); ?></textarea></div>
        <div class="form-group"><label class="form-label">Category</label>
          <select name="category" class="form-input form-select">
            <?php foreach(['Announcement','Field Report','Health','Education','General'] as $cat): ?>
            <option <?php echo ($editPost['category']??'')===$cat?'selected':''; ?>><?php echo $cat; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Cover Image</label>
          <?php uploadWidget('blog-img','image_url',$editPost['image_url']??'','image/*','Image'); ?>
        </div>
        <div class="form-group"><label class="form-label">Thumbnail Image (Optional)</label>
          <?php uploadWidget('blog-thumb','thumbnail_url',$editPost['thumbnail_url']??'','image/*','Image'); ?>
        </div>
        <button class="btn btn-primary" style="width:100%;">Save Post</button>
      </form>
    </div>
  </div>
</div>

<?php elseif ($tab === 'gallery'):
    $photos = $pdo->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll();
?>
<div class="section-header">
  <div><p class="section-title">Gallery</p><p class="section-sub">Photos displayed on the public gallery page.</p></div>
</div>
<div style="display:grid;grid-template-columns:1fr 320px;gap:16px;align-items:start;">
  <div class="card card-body" style="padding:16px;">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:10px;">
      <?php foreach ($photos as $p): ?>
      <div style="position:relative;border-radius:8px;overflow:hidden;border:1px solid var(--border);">
        <img src="<?php echo htmlspecialchars($p['image_url']); ?>" style="width:100%;height:90px;object-fit:cover;display:block;">
        <div style="padding:5px 8px;font-size:11px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo htmlspecialchars($p['caption']??''); ?></div>
        <form method="POST" onsubmit="return confirm('Remove?');" style="position:absolute;top:4px;right:4px;">
          <input type="hidden" name="gallery_action" value="delete">
          <input type="hidden" name="gallery_id" value="<?php echo $p['id']; ?>">
          <button class="btn btn-sm btn-danger btn-icon" style="opacity:.85;"><i data-lucide="x" style="width:12px;height:12px;"></i></button>
        </form>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><h2>Add Photo</h2></div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="gallery_action" value="add">
        <div class="form-group"><label class="form-label">Caption</label><input type="text" name="title" class="form-input" placeholder="Short description..."></div>
        <div class="form-group"><label class="form-label">Photo</label>
          <?php uploadWidget('gal-img','image_url','','image/*','Photo'); ?>
        </div>
        <button class="btn btn-primary" style="width:100%;margin-top:8px;">Add Photo</button>
      </form>
    </div>
  </div>
</div>

<?php elseif ($tab === 'broadcast'):
    $broads = $pdo->query("SELECT * FROM broadcasts ORDER BY sent_at DESC LIMIT 20")->fetchAll();
?>
<div style="display:grid;grid-template-columns:1fr 360px;gap:16px;align-items:start;">
  <div class="card">
    <div class="card-header"><h2>Sent Broadcasts</h2></div>
    <table class="data-table">
      <thead><tr><th>Subject</th><th>Audience</th><th>Sent</th></tr></thead>
      <tbody>
        <?php foreach ($broads as $b): ?>
        <tr><td><?php echo htmlspecialchars($b['subject']); ?></td><td><span class="badge-pill badge-blue"><?php echo $b['target_group']; ?></span></td><td style="font-size:12px;color:var(--muted);"><?php echo isset($b['sent_at'])?date('M j, Y',strtotime($b['sent_at'])):'—'; ?></td></tr>
        <?php endforeach; if(!$broads): ?><tr><td colspan="3" style="text-align:center;color:var(--muted);padding:20px;">No broadcasts sent yet</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="card">
    <div class="card-header"><h2>New Broadcast</h2></div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="broadcast_action" value="1">
        <div class="form-group"><label class="form-label">Subject</label><input type="text" name="subject" class="form-input" required></div>
        <div class="form-group"><label class="form-label">Message</label><textarea name="content" class="form-input" rows="5" required></textarea></div>
        <div class="form-group"><label class="form-label">Audience</label>
          <select name="target_group" class="form-input form-select">
            <option value="all">All Donors</option>
            <option value="donors">Active Donors</option>
            <option value="newsletter">Newsletter Subscribers</option>
          </select>
        </div>
        <button class="btn btn-primary" style="width:100%;">Send Broadcast</button>
      </form>
    </div>
  </div>
</div>

<?php elseif ($tab === 'users'):
    $donors = $pdo->query("SELECT u.*, (SELECT COUNT(*) FROM donations WHERE user_id=u.id) as donation_count, (SELECT COALESCE(SUM(amount),0) FROM donations WHERE user_id=u.id) as total_given FROM users u ORDER BY u.created_at DESC")->fetchAll();
?>
<div class="section-header"><p class="section-title">Donors</p></div>
<div class="card">
  <table class="data-table">
    <thead><tr><th>Name</th><th>Email</th><th>Donations</th><th>Total Given</th><th>Joined</th></tr></thead>
    <tbody>
      <?php foreach ($donors as $d): ?>
      <tr>
        <td><strong><?php echo htmlspecialchars($d['name']??$d['username']??'—'); ?></strong></td>
        <td style="font-size:12px;"><?php echo htmlspecialchars($d['email']); ?></td>
        <td><?php echo $d['donation_count']; ?> donations</td>
        <td>₦<?php echo number_format($d['total_given']); ?></td>
        <td style="font-size:12px;color:var(--muted);"><?php echo isset($d['created_at'])?date('M j, Y',strtotime($d['created_at'])):'—'; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php elseif ($tab === 'components'):
    $stats = $pdo->query("SELECT * FROM impact_stats ORDER BY id")->fetchAll();
?>
<div class="section-header"><p class="section-title">Impact Stats</p><p class="section-sub">Numbers shown to visitors — keep them up to date.</p></div>
<div class="card">
  <form method="POST">
    <div class="card-body">
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;">
        <?php foreach ($stats as $s): ?>
        <div style="background:#f9fafb;border:1px solid var(--border);border-radius:10px;padding:14px;">
          <label class="form-label"><?php echo htmlspecialchars($s['label']); ?></label>
          <input type="text" name="stats[<?php echo $s['id']; ?>][value]" value="<?php echo htmlspecialchars($s['stat_value']); ?>" class="form-input" style="margin-bottom:6px;">
          <input type="text" name="stats[<?php echo $s['id']; ?>][label]" value="<?php echo htmlspecialchars($s['label']); ?>" class="form-input" placeholder="Label">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div style="padding:12px 20px;border-top:1px solid var(--border);">
      <button name="update_stats" class="btn btn-primary">Save Stats</button>
    </div>
  </form>
</div>

<?php elseif ($tab === 'objectives'):
    $objs = $pdo->query("SELECT * FROM objectives ORDER BY id")->fetchAll();
?>
<div class="section-header"><p class="section-title">Aims & Objectives</p><p class="section-sub">Shown on the About page under Article 3.</p></div>
<div class="card">
  <form method="POST">
    <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
      <?php foreach ($objs as $o): ?>
      <div style="background:#f9fafb;border:1px solid var(--border);border-radius:10px;padding:14px;">
        <div class="form-grid">
          <div><label class="form-label">Title</label><input type="text" name="objectives[<?php echo $o['id']; ?>][title]" value="<?php echo htmlspecialchars($o['title']); ?>" class="form-input"></div>
          <div><label class="form-label">Description</label><input type="text" name="objectives[<?php echo $o['id']; ?>][description]" value="<?php echo htmlspecialchars($o['description']); ?>" class="form-input"></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div style="padding:12px 20px;border-top:1px solid var(--border);">
      <button name="update_objectives" class="btn btn-primary">Save Objectives</button>
    </div>
  </form>
</div>

<?php elseif ($tab === 'settings'): ?>
<div class="section-header"><p class="section-title">Settings</p><p class="section-sub">Core site-wide configuration.</p></div>
<form method="POST">
  <input type="hidden" name="update_settings" value="1">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div class="card">
      <div class="card-header"><h2>Identity</h2></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
        <?php foreach(['site_name'=>'Site Name','chairman'=>'Chairman','secretary'=>'Secretary','registration_number'=>'Reg. Number','registration_date'=>'Registration Date'] as $k=>$lbl): ?>
        <div><label class="form-label"><?php echo $lbl; ?></label><input type="text" name="settings[<?php echo $k; ?>]" value="<?php echo htmlspecialchars(getSetting($k,$pdo)); ?>" class="form-input"></div>
        <?php endforeach; ?>
        <div><label class="form-label">Address</label><textarea name="settings[address]" class="form-input" rows="2"><?php echo htmlspecialchars(getSetting('address',$pdo)); ?></textarea></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><h2>Contact & Social</h2></div>
      <div class="card-body" style="display:flex;flex-direction:column;gap:10px;">
        <?php foreach(['contact_email'=>'Email','contact_phone_1'=>'Phone 1','contact_phone_2'=>'Phone 2','facebook_url'=>'Facebook URL','twitter_url'=>'Twitter / X URL','instagram_url'=>'Instagram URL'] as $k=>$lbl): ?>
        <div><label class="form-label"><?php echo $lbl; ?></label><input type="text" name="settings[<?php echo $k; ?>]" value="<?php echo htmlspecialchars(getSetting($k,$pdo)); ?>" class="form-input"></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div style="margin-top:14px;"><button class="btn btn-primary">Save Settings</button></div>
</form>

<?php else:
    // CMS tabs are in the second partial
    include 'includes/admin_cms.php';
endif; ?>
