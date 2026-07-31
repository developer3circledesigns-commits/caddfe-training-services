<?php
require_once __DIR__ . '/../config/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_id'])) {
    header('Location: login');
    exit;
}

$message = '';
$msg_type = '';

function csrf_token(): string {
    return hash_hmac('sha256', session_id(), 'caddfe_admin_csrf');
}

function validate_csrf(string $token): bool {
    return hash_equals(csrf_token(), $token);
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['action'])) {
    try {
        if ($pdo === null) throw new \RuntimeException('DB unavailable');

        if (!isset($_POST['csrf_token']) || !validate_csrf($_POST['csrf_token'])) {
            error_log('Admin CSRF mismatch: SID=' . session_id());
            throw new \RuntimeException('Invalid or expired form. Please reload the page.');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) throw new \InvalidArgumentException('Invalid ID');

        $record_type = $_POST['record_type'] ?? 'enrollment';
        if (!in_array($record_type, ['enrollment', 'enquiry'], true)) {
            throw new \InvalidArgumentException('Invalid record type');
        }
        $table = $record_type === 'enrollment' ? 'enrollments' : 'contact_submissions';
        $label = $record_type === 'enrollment' ? 'Enrollment' : 'Enquiry';

        if ($_POST['action'] === 'update_status') {
            $status = $_POST['status'] ?? '';
            $allowed = ['pending', 'contacted', 'enrolled', 'cancelled'];
            if (!in_array($status, $allowed)) throw new \InvalidArgumentException('Invalid status');

            $pdo->prepare("UPDATE $table SET status = :status WHERE id = :id")
                ->execute([':status' => $status, ':id' => $id]);

            $message = "$label status updated successfully.";
            $msg_type = 'success';
        } elseif ($_POST['action'] === 'delete') {
            $pdo->prepare("DELETE FROM $table WHERE id = :id")
                ->execute([':id' => $id]);

            $message = "$label deleted successfully.";
            $msg_type = 'success';
        }
    } catch (\Throwable $e) {
        $message = 'Operation failed: ' . $e->getMessage();
        $msg_type = 'danger';
        error_log('Admin action error: ' . $e->getMessage());
    }
}

$search = trim($_GET['search'] ?? '');
$allowed_statuses = ['pending', 'contacted', 'enrolled', 'cancelled'];
$status_filter = $_GET['status'] ?? '';
if ($status_filter !== '' && !in_array($status_filter, $allowed_statuses, true)) {
    $status_filter = '';
}
$course_filter = trim($_GET['course'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 12;
$offset = ($page - 1) * $per_page;

$search_params = [];
$search_where = '';

if ($search !== '') {
    $search_where = 'AND (e.full_name LIKE :search OR e.email LIKE :search2 OR e.phone LIKE :search3)';
    $search_params[':search'] = "%$search%";
    $search_params[':search2'] = "%$search%";
    $search_params[':search3'] = "%$search%";
}

$status_where = '';
$status_params = [];
if ($status_filter !== '') {
    $status_where = 'AND e.status = :status';
    $status_params[':status'] = $status_filter;
}

$course_where = '';
$course_params = [];
if ($course_filter !== '') {
    $course_where = 'AND e.course_name LIKE :course';
    $course_params[':course'] = "%$course_filter%";
}

try {
    if ($pdo === null) throw new \RuntimeException('Database connection not available');

    $stats = [];
    $has_status_c = $has_status ?? false;
    if (!$has_status_c) {
        try {
            $check = $pdo->query("SHOW COLUMNS FROM contact_submissions LIKE 'status'");
            $has_status_c = (bool)$check->fetch();
        } catch (\Throwable $e) {}
    }
    $total_stmt = $pdo->query('SELECT COUNT(*) FROM enrollments');
    $enr_total = (int)$total_stmt->fetchColumn();
    $enq_total_stmt = $pdo->query('SELECT COUNT(*) FROM contact_submissions');
    $enq_total = (int)$enq_total_stmt->fetchColumn();
    $stats['total'] = $enr_total + $enq_total;
    $stats['total_enquiries'] = $enq_total;

    $enr_stats = $pdo->query("SELECT
        SUM(status='pending') as pending, SUM(status='contacted') as contacted,
        SUM(status='enrolled') as enrolled, SUM(status='cancelled') as cancelled
        FROM enrollments")->fetch();

    if ($has_status_c) {
        $enq_stats = $pdo->query("SELECT
            SUM(status='pending') as pending, SUM(status='contacted') as contacted,
            SUM(status='enrolled') as enrolled, SUM(status='cancelled') as cancelled
            FROM contact_submissions")->fetch();
    } else {
        $enq_stats = ['pending' => 0, 'contacted' => 0, 'enrolled' => 0, 'cancelled' => 0];
    }

    $stats['pending'] = (int)$enr_stats['pending'] + (int)$enq_stats['pending'];
    $stats['contacted'] = (int)$enr_stats['contacted'] + (int)$enq_stats['contacted'];
    $stats['enrolled'] = (int)$enr_stats['enrolled'] + (int)$enq_stats['enrolled'];
    $stats['cancelled'] = (int)$enr_stats['cancelled'] + (int)$enq_stats['cancelled'];

    $enq_stmt = $pdo->query('SELECT COUNT(*) FROM contact_submissions');
    $stats['total_enquiries'] = (int)$enq_stmt->fetchColumn();

    $c_status_where = '';
    if ($status_filter !== '') {
        if ($has_status_c) {
            $c_status_where = 'AND c.status = :c_status';
        } elseif ($status_filter === 'pending') {
            $c_status_where = 'AND 1=1';
        } else {
            $c_status_where = 'AND 1=0';
        }
    }
    $c_course_where = '';
    if ($course_filter !== '') {
        $c_course_where = 'AND c.subject LIKE :c_course';
    }

    $count_params = array_merge($search_params, $status_params, $course_params);
    $count_sql = "SELECT COUNT(*) FROM (
        SELECT id, created_at FROM enrollments e WHERE 1=1 $search_where $status_where $course_where
        UNION ALL
        SELECT id, created_at FROM contact_submissions c WHERE 1=1 " . ($search !== '' ? 'AND (c.full_name LIKE :search_c OR c.email LIKE :search2_c OR c.phone LIKE :search3_c)' : '') . " $c_status_where $c_course_where
    ) combined";
    if ($search !== '') {
        $count_params[':search_c'] = "%$search%";
        $count_params[':search2_c'] = "%$search%";
        $count_params[':search3_c'] = "%$search%";
    }
    if ($status_filter !== '' && $has_status_c) {
        $count_params[':c_status'] = $status_filter;
    }
    if ($course_filter !== '') {
        $count_params[':c_course'] = "%$course_filter%";
    }
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($count_params);
    $total_records = (int)$count_stmt->fetchColumn();
    $total_pages = max(1, ceil($total_records / $per_page));

    $has_status = false;
    try {
        $check = $pdo->query("SHOW COLUMNS FROM contact_submissions LIKE 'status'");
        $has_status = (bool)$check->fetch();
    } catch (\Throwable $e) {}

    $enquiry_status_col = $has_status ? 'c.status' : "'pending' as status";

    $union_params = array_merge($search_params, $status_params, $course_params);
    $union_sql = "
        (SELECT 'enrollment' as record_type, e.id, e.full_name, e.email, e.phone, e.created_at,
                e.status, e.course_name, e.education, e.dob, e.address,
                e.photo_data, e.photo_mime, e.enquiry_source, e.ip_address, e.user_agent,
                NULL as subject, NULL as message, NULL as enquiry_id
         FROM enrollments e
         WHERE 1=1 $search_where $status_where $course_where)
        UNION ALL
        (SELECT 'enquiry' as record_type, c.id, c.full_name, c.email, c.phone, c.created_at,
                $enquiry_status_col, NULL as course_name, NULL as education, NULL as dob, NULL as address,
                NULL as photo_data, NULL as photo_mime, NULL as enquiry_source, c.ip_address, c.user_agent,
                c.subject, c.message, c.id as enquiry_id
         FROM contact_submissions c
         WHERE 1=1 " . ($search !== '' ? 'AND (c.full_name LIKE :search_c2 OR c.email LIKE :search2_c2 OR c.phone LIKE :search3_c2)' : '') . " $c_status_where $c_course_where)
        ORDER BY created_at DESC
        LIMIT $per_page OFFSET $offset";
    if ($search !== '') {
        $union_params[':search_c2'] = "%$search%";
        $union_params[':search2_c2'] = "%$search%";
        $union_params[':search3_c2'] = "%$search%";
    }
    if ($status_filter !== '' && $has_status_c) {
        $union_params[':c_status'] = $status_filter;
    }
    if ($course_filter !== '') {
        $union_params[':c_course'] = "%$course_filter%";
    }
    $stmt = $pdo->prepare($union_sql);
    $stmt->execute($union_params);
    $records = $stmt->fetchAll();
} catch (\Throwable $e) {
    $message = 'Failed to load records: ' . $e->getMessage();
    $msg_type = 'danger';
    error_log('Admin fetch error: ' . $e->getMessage());
    $records = [];
    $stats = ['total' => 0, 'pending' => 0, 'contacted' => 0, 'enrolled' => 0, 'cancelled' => 0, 'total_enquiries' => 0];
    $total_records = 0;
    $total_pages = 1;
}

$courses_filter_list = [];
try {
    if ($pdo !== null) {
        $stmt = $pdo->query('SELECT name FROM courses WHERE is_active = 1 ORDER BY display_order ASC');
        $courses_filter_list = $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
} catch (\Throwable $e) {}

function getStatusBadge(string $status): string {
    return match ($status) {
        'pending' => 'badge-pending',
        'contacted' => 'badge-contacted',
        'enrolled' => 'badge-enrolled',
        'cancelled' => 'badge-cancelled',
        default => 'bg-secondary',
    };
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - CADDFE Training Services</title>
  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/bootstrap-icons.min.css">
  <link href="../css/inter.css" rel="stylesheet">
  <style>
    * { font-family: 'Inter', system-ui, sans-serif; }
    body { background: #f8fafc; }

    .topbar {
      background: #0f172a; padding: 0.85rem 2rem; display: flex; align-items: center;
      justify-content: space-between; position: sticky; top: 0; z-index: 999;
    }
    .topbar .page-title h5 { margin: 0; font-weight: 700; color: #fff; }
    .topbar .page-title small { color: #94a3b8; font-size: 0.8rem; }
    .topbar .admin-profile { display: flex; align-items: center; gap: 1rem; }
    .topbar .admin-profile .admin-info { color: #cbd5e1; font-size: 0.85rem; }
    .topbar .admin-profile .admin-info .role-badge {
      background: #1e293b; color: #94a3b8; font-size: 0.7rem; padding: 0.15rem 0.5rem; border-radius: 50px;
    }

    .content-body { padding: 1.5rem 2rem; }

    .stat-pill {
      background: #fff; border-radius: 0; padding: 1rem 1.5rem;
      border: 1px solid #e9ecef; display: flex; align-items: center; gap: 1rem;
    }
    .stat-pill .sp-icon {
      width: 44px; height: 44px; border-radius: 0;
      display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
    }
    .stat-pill .sp-number { font-size: 1.4rem; font-weight: 700; line-height: 1; color: #1e293b; }
    .stat-pill .sp-label { font-size: 0.78rem; color: #64748b; margin-top: 2px; }

    .filter-bar {
      background: #fff; border-radius: 0; padding: 1rem 1.5rem;
      border: 1px solid #e9ecef; display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem;
    }
    .filter-bar .form-control, .filter-bar .form-select {
      font-size: 0.85rem; border-radius: 10px; border-color: #e2e8f0;
    }
    .filter-bar .form-control:focus, .filter-bar .form-select:focus {
      border-color: #d8000d; box-shadow: 0 0 0 3px rgba(216,0,13,0.1);
    }

    .enrollment-card {
      background: #fff; border-radius: 0; border: 1px solid #e9ecef;
      overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; height: 100%;
    }
    .enrollment-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
    .enrollment-card .card-header {
      padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.75rem;
      border-bottom: 1px solid #f1f5f9; background: #fff;
    }
    .enrollment-card .card-header .e-avatar {
      width: 44px; height: 44px; border-radius: 50%; object-fit: cover; background: #e9ecef;
      flex-shrink: 0;
    }
    .enrollment-card .card-header .e-name { font-weight: 600; font-size: 0.95rem; color: #1e293b; }
    .enrollment-card .card-header .e-email { font-size: 0.8rem; color: #64748b; }
    .enrollment-card .card-body { padding: 1rem 1.25rem; }
    .enrollment-card .e-detail { display: flex; gap: 0.5rem; margin-bottom: 0.45rem; font-size: 0.85rem; }
    .enrollment-card .e-detail .e-label { color: #64748b; min-width: 80px; flex-shrink: 0; }
    .enrollment-card .e-detail span:last-child { color: #1e293b; }
    .enrollment-card .card-footer {
      padding: 0.75rem 1.25rem; background: #fafbfc;
      border-top: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;
      flex-wrap: wrap; gap: 0.5rem;
    }

    .badge-status {
      font-size: 0.72rem; font-weight: 500; padding: 0.3em 0.75em; border-radius: 50px;
    }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-contacted { background: #cce5ff; color: #004085; }
    .badge-enrolled { background: #d4edda; color: #155724; }
    .badge-cancelled { background: #f8d7da; color: #721c24; }

    .btn-primary-custom {
      background: #d8000d; border-color: #d8000d; color: #fff;
    }
    .btn-primary-custom:hover {
      background: #b3000a; border-color: #b3000a; color: #fff;
    }
    .btn-outline-primary-custom {
      border-color: #d8000d; color: #d8000d;
    }
    .btn-outline-primary-custom:hover {
      background: #d8000d; color: #fff;
    }
    .btn-action-sm { padding: 0.3rem 0.55rem; font-size: 0.78rem; border-radius: 8px; }

    .empty-state { text-align: center; padding: 4rem 2rem; }
    .empty-state i { font-size: 3rem; color: #e2e8f0; margin-bottom: 1rem; }
    .empty-state h5 { color: #64748b; }
    .empty-state p { color: #94a3b8; font-size: 0.9rem; }

    .source-tag {
      font-size: 0.68rem; padding: 0.2em 0.55em; border-radius: 4px; background: #f1f5f9; color: #475569;
    }
    .source-tag-web { background: #fef2f2; color: #d8000d; }
    .source-tag-whatsapp { background: #e6f7ee; color: #198754; }

    .page-link {
      border-radius: 8px; border-color: #e2e8f0; color: #475569; font-size: 0.8rem; padding: 0.35rem 0.7rem;
    }
    .page-item.active .page-link {
      background: #d8000d; border-color: #d8000d; color: #fff;
    }
    .page-item.active .page-link:hover { background: #b3000a; }

    .modal-content { border-radius: 0; border: none; }
    .modal-header { border-bottom-color: #f1f5f9; }
    .modal-footer { border-top-color: #f1f5f9; }

    @media (max-width:768px) {
      .content-body { padding: 1rem; }
      .stat-pill { padding: 0.75rem 1rem; }
      .topbar { padding: 0.75rem 1rem; }
    }
  </style>
</head>
<body>

<div class="topbar">
  <div class="d-flex align-items-center gap-3">
    <img src="../images/Caddfe Logo 300x150.png" alt="CADDFE" style="height:32px;width:auto;">
    <!-- <div class="page-title">
      <h5 style="color:#fff;margin:0;font-weight:700;">Enrollments</h5>
      <small style="color:#94a3b8;font-size:0.8rem;">Manage all student enrollment submissions</small>
    </div> -->
  </div>
  <div class="admin-profile">
    <span class="admin-info">
      <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['admin_name']) ?>
      <span class="role-badge ms-1"><?= htmlspecialchars($_SESSION['admin_role']) ?></span>
    </span>
    <button type="button" data-bs-toggle="modal" data-bs-target="#logoutModal" class="btn btn-sm" style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);color:#cbd5e1;border-radius:8px;cursor:pointer;">
      <i class="bi bi-box-arrow-right"></i>
    </button>
  </div>
</div>

<div class="content-body">
  <?php if ($message): ?>
    <div class="alert alert-<?= $msg_type === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show py-2 small" role="alert" style="border-radius:10px;">
      <?php if ($msg_type === 'success'): ?>
        <i class="bi bi-check-circle me-1 text-success"></i>
      <?php else: ?>
        <i class="bi bi-exclamation-circle me-1 text-danger"></i>
      <?php endif; ?>
      <?= htmlspecialchars($message) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size:0.75rem;"></button>
    </div>
  <?php endif; ?>


  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="stat-pill">
        <div class="sp-icon" style="background:#fef2f2;color:#d8000d;"><i class="bi bi-envelope-open"></i></div>
        <div><div class="sp-number"><?= (int)$stats['total_enquiries'] ?></div><div class="sp-label">Total Enquiries</div></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-pill">
        <div class="sp-icon" style="background:#f8d7da;color:#721c24;"><i class="bi bi-clock-history"></i></div>
        <div><div class="sp-number"><?= (int)$stats['pending'] ?></div><div class="sp-label">Not Contacted</div></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-pill">
        <div class="sp-icon" style="background:#cce5ff;color:#004085;"><i class="bi bi-telephone"></i></div>
        <div><div class="sp-number"><?= (int)$stats['contacted'] ?></div><div class="sp-label">Contacted</div></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-pill">
        <div class="sp-icon" style="background:#d4edda;color:#155724;"><i class="bi bi-check-circle"></i></div>
        <div><div class="sp-number"><?= (int)$stats['enrolled'] ?></div><div class="sp-label">Enrolled</div></div>
      </div>
    </div>
  </div>

  <form class="filter-bar mb-4" method="get">
    <div style="flex:1;min-width:180px;">
      <input type="text" name="search" class="form-control" placeholder="Search name, email, phone..." value="<?= htmlspecialchars($search) ?>">
    </div>
    <div style="width:150px;">
      <select name="status" class="form-select">
        <option value="">All Status</option>
        <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="contacted" <?= $status_filter === 'contacted' ? 'selected' : '' ?>>Contacted</option>
        <option value="enrolled" <?= $status_filter === 'enrolled' ? 'selected' : '' ?>>Enrolled</option>
        <option value="cancelled" <?= $status_filter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
      </select>
    </div>
    <div style="width:160px;">
      <select name="course" class="form-select">
        <option value="">All Courses</option>
        <?php foreach ($courses_filter_list as $c): ?>
          <option value="<?= htmlspecialchars($c) ?>" <?= $course_filter === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn btn-primary-custom" style="border-radius:10px;"><i class="bi bi-funnel me-1"></i>Apply</button>
    <a href="index" class="btn btn-outline-secondary" style="border-radius:10px;"><i class="bi bi-arrow-counterclockwise"></i></a>
  </form>

  <?php if (count($records) > 0): ?>
    <div class="row g-3">
      <?php foreach ($records as $r): ?>
        <div class="col-xl-4 col-md-6">
          <div class="enrollment-card">
            <div class="card-header">
              <?php if ($r['record_type'] === 'enrollment'): ?>
                <?php if (!empty($r['photo_data']) && !empty($r['photo_mime'])): ?>
                  <img src="photo.php?id=<?= (int)$r['id'] ?>" class="e-avatar" alt="">
                <?php else: ?>
                  <img src="https://ui-avatars.com/api/?name=<?= urlencode($r['full_name']) ?>&background=d8000d&color=fff&size=44" class="e-avatar" alt="">
                <?php endif; ?>
              <?php else: ?>
                <div class="e-avatar d-flex align-items-center justify-content-center" style="background:#fef2f2;color:#d8000d;font-size:1.2rem;">
                  <i class="bi bi-envelope"></i>
                </div>
              <?php endif; ?>
              <div style="overflow:hidden;">
                <div class="e-name text-truncate"><?= htmlspecialchars($r['full_name']) ?></div>
                <div class="e-email text-truncate"><?= htmlspecialchars($r['email']) ?></div>
              </div>
              <span class="badge-status <?= getStatusBadge($r['status']) ?> ms-auto"><?= ucfirst(htmlspecialchars($r['status'])) ?></span>
            </div>
            <div class="card-body">
              <div class="e-detail"><span class="e-label">Phone</span><span><?= htmlspecialchars($r['phone']) ?></span></div>
              <?php if ($r['record_type'] === 'enrollment'): ?>
                <div class="e-detail"><span class="e-label">Course</span><span><?= htmlspecialchars($r['course_name']) ?></span></div>
                <div class="e-detail"><span class="e-label">Education</span><span><?= htmlspecialchars($r['education'] ?? '-') ?></span></div>
                <div class="e-detail"><span class="e-label">DOB</span><span><?= $r['dob'] ? htmlspecialchars($r['dob']) : '-' ?></span></div>
                <?php if (!empty($r['address'])): ?>
                  <div class="e-detail"><span class="e-label">Address</span><span class="text-truncate"><?= htmlspecialchars($r['address']) ?></span></div>
                <?php endif; ?>
              <?php else: ?>
                <div class="e-detail"><span class="e-label">Subject</span><span><?= htmlspecialchars($r['subject'] ?? '-') ?></span></div>
                <div class="e-detail"><span class="e-label">Message</span><span class="text-truncate"><?= htmlspecialchars(mb_substr($r['message'], 0, 100)) ?><?= mb_strlen($r['message']) > 100 ? '...' : '' ?></span></div>
              <?php endif; ?>
              <div class="e-detail"><span class="e-label">Submitted</span><span><?= date('Y-m-d h:i A', strtotime($r['created_at'])) ?></span></div>
            </div>
            <div class="card-footer">
              <?php if ($r['record_type'] === 'enrollment'): ?>
                <span class="source-tag source-tag-<?= htmlspecialchars($r['enquiry_source']) ?>">
                  <i class="bi bi-<?= $r['enquiry_source'] === 'whatsapp' ? 'whatsapp' : 'globe' ?> me-1"></i><?= ucfirst(htmlspecialchars($r['enquiry_source'] ?? 'web')) ?>
                </span>
              <?php else: ?>
                <span class="source-tag" style="background:#f1f5f9;color:#475569;">
                  <i class="bi bi-globe me-1"></i>Web
                </span>
              <?php endif; ?>
              <div class="d-flex gap-1">
                <button class="btn btn-outline-primary-custom btn-action-sm" data-bs-toggle="modal" data-bs-target="#detailModal<?= (int)$r['id'] ?>_<?= $r['record_type'] ?>" title="View Details"><i class="bi bi-eye"></i></button>
                <div class="btn-group">
                  <button class="btn btn-outline-secondary btn-action-sm dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <form method="post" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="record_type" value="<?= $r['record_type'] ?>">
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="status" value="pending">
                        <button type="submit" class="dropdown-item"><i class="bi bi-clock-history me-2"></i>Mark Not Contacted</button>
                      </form>
                    </li>
                    <li>
                      <form method="post" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="record_type" value="<?= $r['record_type'] ?>">
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="status" value="contacted">
                        <button type="submit" class="dropdown-item"><i class="bi bi-telephone me-2"></i>Mark Contacted</button>
                      </form>
                    </li>
                    <li>
                      <form method="post" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="record_type" value="<?= $r['record_type'] ?>">
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="status" value="enrolled">
                        <button type="submit" class="dropdown-item"><i class="bi bi-check-circle me-2"></i>Mark Enrolled</button>
                      </form>
                    </li>
                    <li>
                      <form method="post" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="record_type" value="<?= $r['record_type'] ?>">
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="status" value="cancelled">
                        <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#confirmCancelModal"><i class="bi bi-x-circle me-2"></i>Cancelled / Not Interested</button>
                      </form>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                      <form method="post" class="d-inline" onsubmit="return confirm('Delete this record?')">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="record_type" value="<?= $r['record_type'] ?>">
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Delete</button>
                      </form>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="detailModal<?= (int)$r['id'] ?>_<?= $r['record_type'] ?>" tabindex="-1">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" style="color:#1e293b;"><?= $r['record_type'] === 'enrollment' ? 'Enrollment' : 'Enquiry' ?> Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <?php if ($r['record_type'] === 'enrollment'): ?>
                <div class="row g-3">
                  <div class="col-md-4 text-center mb-3">
                    <?php if (!empty($r['photo_data']) && !empty($r['photo_mime'])): ?>
                      <img src="photo.php?id=<?= (int)$r['id'] ?>" alt="" style="width:120px;height:120px;border-radius:50%;object-fit:cover;background:#e9ecef;">
                    <?php else: ?>
                      <img src="https://ui-avatars.com/api/?name=<?= urlencode($r['full_name']) ?>&background=d8000d&color=fff&size=120" alt="" style="width:120px;height:120px;border-radius:50%;">
                    <?php endif; ?>
                    <div class="mt-2">
                      <span class="badge-status <?= getStatusBadge($r['status']) ?>"><?= ucfirst(htmlspecialchars($r['status'])) ?></span>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <table class="table table-sm table-borderless" style="font-size:0.88rem;">
                      <tr><td class="text-muted" style="width:120px;color:#64748b;">Full Name</td><td style="color:#1e293b;"><strong><?= htmlspecialchars($r['full_name']) ?></strong></td></tr>
                      <tr><td class="text-muted" style="color:#64748b;">Email</td><td style="color:#1e293b;"><?= htmlspecialchars($r['email']) ?></td></tr>
                      <tr><td class="text-muted" style="color:#64748b;">Phone</td><td style="color:#1e293b;"><?= htmlspecialchars($r['phone']) ?></td></tr>
                      <tr><td class="text-muted" style="color:#64748b;">Date of Birth</td><td style="color:#1e293b;"><?= $r['dob'] ? htmlspecialchars($r['dob']) : '-' ?></td></tr>
                      <tr><td class="text-muted" style="color:#64748b;">Course</td><td style="color:#1e293b;"><?= htmlspecialchars($r['course_name']) ?></td></tr>
                      <tr><td class="text-muted" style="color:#64748b;">Education</td><td style="color:#1e293b;"><?= htmlspecialchars($r['education'] ?? '-') ?></td></tr>
                      <tr><td class="text-muted" style="color:#64748b;">Source</td><td style="color:#1e293b;"><?= ucfirst(htmlspecialchars($r['enquiry_source'] ?? 'web')) ?></td></tr>
                      <tr><td class="text-muted" style="color:#64748b;">Submitted</td><td style="color:#1e293b;"><?= date('Y-m-d h:i A', strtotime($r['created_at'])) ?></td></tr>
                      <tr><td class="text-muted" style="color:#64748b;">IP Address</td><td style="color:#1e293b;" class="small"><?= htmlspecialchars($r['ip_address'] ?? '-') ?></td></tr>
                    </table>
                  </div>
                </div>
                <?php if (!empty($r['address'])): ?>
                  <div class="mt-2 pt-2 border-top" style="border-color:#f1f5f9 !important;">
                    <small style="color:#64748b;">Address:</small>
                    <p class="mb-0" style="color:#1e293b;"><?= nl2br(htmlspecialchars($r['address'])) ?></p>
                  </div>
                <?php endif; ?>
                <?php else: ?>
                <table class="table table-sm table-borderless" style="font-size:0.88rem;">
                  <tr><td class="text-muted" style="width:120px;color:#64748b;">Full Name</td><td style="color:#1e293b;"><strong><?= htmlspecialchars($r['full_name']) ?></strong></td></tr>
                  <tr><td class="text-muted" style="color:#64748b;">Email</td><td style="color:#1e293b;"><?= htmlspecialchars($r['email']) ?></td></tr>
                  <tr><td class="text-muted" style="color:#64748b;">Phone</td><td style="color:#1e293b;"><?= htmlspecialchars($r['phone'] ?? '-') ?></td></tr>
                  <tr><td class="text-muted" style="color:#64748b;">Subject</td><td style="color:#1e293b;"><?= htmlspecialchars($r['subject'] ?? '-') ?></td></tr>
                  <tr><td class="text-muted" style="color:#64748b;">Submitted</td><td style="color:#1e293b;"><?= date('Y-m-d h:i A', strtotime($r['created_at'])) ?></td></tr>
                  <tr><td class="text-muted" style="color:#64748b;">IP Address</td><td style="color:#1e293b;" class="small"><?= htmlspecialchars($r['ip_address'] ?? '-') ?></td></tr>
                </table>
                <div class="mt-2 pt-2 border-top" style="border-color:#f1f5f9 !important;">
                  <small style="color:#64748b;">Message:</small>
                  <p class="mb-0 mt-1" style="color:#1e293b;white-space:pre-wrap;"><?= htmlspecialchars($r['message']) ?></p>
                </div>
                <?php endif; ?>
              </div>
              <div class="modal-footer">
                <form method="post" class="d-inline">
                  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                  <input type="hidden" name="record_type" value="<?= $r['record_type'] ?>">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <input type="hidden" name="action" value="update_status">
                  <input type="hidden" name="status" value="pending">
                  <button type="submit" class="btn btn-sm" style="background:#fff3cd;border-color:#ffeeba;color:#856404;border-radius:8px;"><i class="bi bi-clock-history me-1"></i>Mark Not Contacted</button>
                </form>
                <form method="post" class="d-inline">
                  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                  <input type="hidden" name="record_type" value="<?= $r['record_type'] ?>">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <input type="hidden" name="action" value="update_status">
                  <input type="hidden" name="status" value="contacted">
                  <button type="submit" class="btn btn-sm" style="background:#cce5ff;border-color:#b8daff;color:#004085;border-radius:8px;"><i class="bi bi-telephone me-1"></i>Mark Contacted</button>
                </form>
                <form method="post" class="d-inline">
                  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                  <input type="hidden" name="record_type" value="<?= $r['record_type'] ?>">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <input type="hidden" name="action" value="update_status">
                  <input type="hidden" name="status" value="enrolled">
                  <button type="submit" class="btn btn-sm" style="background:#d4edda;border-color:#c3e6cb;color:#155724;border-radius:8px;"><i class="bi bi-check-circle me-1"></i>Mark Enrolled</button>
                </form>
                <form method="post" class="d-inline">
                  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                  <input type="hidden" name="record_type" value="<?= $r['record_type'] ?>">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <input type="hidden" name="action" value="update_status">
                  <input type="hidden" name="status" value="cancelled">
                  <button type="button" class="btn btn-sm" style="background:#f8d7da;border-color:#f5c6cb;color:#721c24;border-radius:8px;" data-bs-toggle="modal" data-bs-target="#confirmCancelModal"><i class="bi bi-x-circle me-1"></i>Cancelled / Not Interested</button>
                </form>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" style="border-radius:8px;">Close</button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if ($total_pages > 1): ?>
      <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mt-4 gap-2">
        <div style="color:#64748b;font-size:0.8rem;">
          Showing <?= $offset + 1 ?>–<?= min($offset + $per_page, $total_records) ?> of <?= $total_records ?> record<?= $total_records !== 1 ? 's' : '' ?>
        </div>
        <nav>
          <ul class="pagination pagination-sm mb-0">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&course=<?= urlencode($course_filter) ?>"><i class="bi bi-chevron-left"></i></a>
            </li>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
              <?php if ($i === 1 || $i === $total_pages || abs($i - $page) <= 2): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&course=<?= urlencode($course_filter) ?>"><?= $i ?></a>
                </li>
              <?php elseif ($i === 2 || $i === $total_pages - 1): ?>
                <li class="page-item disabled"><a class="page-link">...</a></li>
              <?php endif; ?>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&course=<?= urlencode($course_filter) ?>"><i class="bi bi-chevron-right"></i></a>
            </li>
          </ul>
        </nav>
      </div>
    <?php else: ?>
      <div class="text-center mt-3" style="color:#94a3b8;font-size:0.85rem;">
        Showing all <?= $total_records ?> record<?= $total_records !== 1 ? 's' : '' ?>
      </div>
    <?php endif; ?>

  <?php else: ?>
    <div class="empty-state">
      <i class="bi bi-inbox"></i>
      <h5>No records found</h5>
      <p>Try adjusting your search or filter criteria.</p>
      <?php if ($search || $status_filter || $course_filter): ?>
        <a href="index" class="btn btn-outline-primary-custom btn-sm rounded-pill px-3"><i class="bi bi-arrow-counterclockwise me-1"></i>Clear Filters</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>



<!-- Confirm Cancel Modal -->
<div class="modal fade" id="confirmCancelModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-body text-center py-4">
        <i class="bi bi-x-circle fs-1 text-danger mb-3 d-block"></i>
        <h6 style="color:#1e293b;">Confirm Cancellation</h6>
        <p class="small" style="color:#64748b;">Mark this record as cancelled / not interested?</p>
        <div class="d-flex gap-2 justify-content-center mt-3">
          <button type="button" class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal" style="border-radius:8px;">No</button>
          <button type="button" id="confirmCancelBtn" class="btn btn-sm px-3 text-white" style="background:#d8000d;border-color:#d8000d;border-radius:8px;">Yes, Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-body text-center py-4">
        <i class="bi bi-box-arrow-right fs-1 text-danger mb-3 d-block"></i>
        <h6 style="color:#1e293b;">Confirm Logout</h6>
        <p class="small" style="color:#64748b;">Are you sure you want to logout?</p>
        <div class="d-flex gap-2 justify-content-center mt-3">
          <button type="button" class="btn btn-sm btn-secondary px-3" data-bs-dismiss="modal" style="border-radius:8px;">Cancel</button>
          <a href="logout" class="btn btn-sm px-3 text-white" style="background:#d8000d;border-color:#d8000d;border-radius:8px;">Logout</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
var cancelForm = null;
document.addEventListener('click', function(e) {
  var btn = e.target.closest('[data-bs-target="#confirmCancelModal"]');
  if (btn) cancelForm = btn.closest('form');
});
document.getElementById('confirmCancelBtn')?.addEventListener('click', function() {
  if (cancelForm) cancelForm.submit();
});
</script>
<script src="../js/bootstrap.bundle.min.js" defer></script>
</body>
</html>