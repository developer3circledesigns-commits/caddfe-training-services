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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        if ($pdo === null) throw new \RuntimeException('DB unavailable');

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) throw new \InvalidArgumentException('Invalid ID');

        if ($_POST['action'] === 'update_status') {
            $status = $_POST['status'] ?? '';
            $allowed = ['pending', 'contacted', 'enrolled', 'cancelled'];
            if (!in_array($status, $allowed)) throw new \InvalidArgumentException('Invalid status');

            $pdo->prepare('UPDATE enrollments SET status = :status WHERE id = :id')
                ->execute([':status' => $status, ':id' => $id]);

            $message = 'Enrollment status updated successfully.';
            $msg_type = 'success';
        } elseif ($_POST['action'] === 'delete') {
            $pdo->prepare('DELETE FROM enrollments WHERE id = :id')
                ->execute([':id' => $id]);

            $message = 'Enrollment deleted successfully.';
            $msg_type = 'success';
        }
    } catch (\Throwable $e) {
        $message = 'Operation failed: ' . $e->getMessage();
        $msg_type = 'danger';
        error_log('Admin action error: ' . $e->getMessage());
    }
}

$search = trim($_GET['search'] ?? '');
$status_filter = $_GET['status'] ?? '';
$course_filter = trim($_GET['course'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 12;
$offset = ($page - 1) * $per_page;

$where = [];
$params = [];

if ($search !== '') {
    $where[] = '(e.full_name LIKE :search OR e.email LIKE :search2 OR e.phone LIKE :search3)';
    $params[':search'] = "%$search%";
    $params[':search2'] = "%$search%";
    $params[':search3'] = "%$search%";
}
if ($status_filter !== '') {
    $where[] = 'e.status = :status';
    $params[':status'] = $status_filter;
}
if ($course_filter !== '') {
    $where[] = 'e.course_name LIKE :course';
    $params[':course'] = "%$course_filter%";
}
$where_sql = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

try {
    if ($pdo === null) throw new \RuntimeException('Database connection not available');

    $stats = [];
    $stmt = $pdo->query("SELECT COUNT(*) as total,
        SUM(status='pending') as pending,
        SUM(status='contacted') as contacted,
        SUM(status='enrolled') as enrolled,
        SUM(status='cancelled') as cancelled
        FROM enrollments");
    $stats = $stmt->fetch();

    $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments e $where_sql");
    $count_stmt->execute($params);
    $total_records = (int)$count_stmt->fetchColumn();
    $total_pages = max(1, ceil($total_records / $per_page));

    $sql = "SELECT e.*, c.name as course_display
            FROM enrollments e
            LEFT JOIN courses c ON e.course_id = c.id
            $where_sql
            ORDER BY e.created_at DESC
            LIMIT $per_page OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $enrollments = $stmt->fetchAll();
} catch (\Throwable $e) {
    $message = 'Failed to load enrollments: ' . $e->getMessage();
    $msg_type = 'danger';
    error_log('Admin enrollments fetch: ' . $e->getMessage());
    $enrollments = [];
    $stats = ['total' => 0, 'pending' => 0, 'contacted' => 0, 'enrolled' => 0, 'cancelled' => 0];
    $total_records = 0;
    $total_pages = 1;
}

$courses_filter_list = [];
try {
    if ($pdo !== null) {
        $stmt = $pdo->query('SELECT DISTINCT course_name FROM enrollments ORDER BY course_name');
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
    <img src="../images/logo.png" alt="CADDFE" style="height:32px;width:auto;">
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
        <div class="sp-icon" style="background:#fef2f2;color:#d8000d;"><i class="bi bi-people"></i></div>
        <div><div class="sp-number"><?= (int)$stats['total'] ?></div><div class="sp-label">Total Enrollments</div></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-pill">
        <div class="sp-icon" style="background:#fff3cd;color:#856404;"><i class="bi bi-clock-history"></i></div>
        <div><div class="sp-number"><?= (int)$stats['pending'] ?></div><div class="sp-label">Pending</div></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-pill">
        <div class="sp-icon" style="background:#d4edda;color:#155724;"><i class="bi bi-check-circle"></i></div>
        <div><div class="sp-number"><?= (int)$stats['enrolled'] ?></div><div class="sp-label">Enrolled</div></div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-pill">
        <div class="sp-icon" style="background:#f8d7da;color:#721c24;"><i class="bi bi-x-circle"></i></div>
        <div><div class="sp-number"><?= (int)$stats['contacted'] + (int)$stats['cancelled'] ?></div><div class="sp-label">Contacted / Cancelled</div></div>
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

  <?php if (count($enrollments) > 0): ?>
    <div class="row g-3">
      <?php foreach ($enrollments as $e): ?>
        <div class="col-xl-4 col-md-6">
          <div class="enrollment-card">
            <div class="card-header">
              <?php if (!empty($e['photo_data']) && !empty($e['photo_mime'])): ?>
                <img src="photo.php?id=<?= (int)$e['id'] ?>" class="e-avatar" alt="">
              <?php else: ?>
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($e['full_name']) ?>&background=d8000d&color=fff&size=44" class="e-avatar" alt="">
              <?php endif; ?>
              <div style="overflow:hidden;">
                <div class="e-name text-truncate"><?= htmlspecialchars($e['full_name']) ?></div>
                <div class="e-email text-truncate"><?= htmlspecialchars($e['email']) ?></div>
              </div>
              <span class="badge-status <?= getStatusBadge($e['status']) ?> ms-auto"><?= ucfirst(htmlspecialchars($e['status'])) ?></span>
            </div>
            <div class="card-body">
              <div class="e-detail"><span class="e-label">Phone</span><span><?= htmlspecialchars($e['phone']) ?></span></div>
              <div class="e-detail"><span class="e-label">Course</span><span><?= htmlspecialchars($e['course_name']) ?></span></div>
              <div class="e-detail"><span class="e-label">Education</span><span><?= htmlspecialchars($e['education'] ?? '-') ?></span></div>
              <div class="e-detail"><span class="e-label">DOB</span><span><?= $e['dob'] ? htmlspecialchars($e['dob']) : '-' ?></span></div>
              <div class="e-detail"><span class="e-label">Submitted</span><span><?= date('Y-m-d h:i A', strtotime($e['created_at'])) ?></span></div>
              <?php if (!empty($e['address'])): ?>
                <div class="e-detail"><span class="e-label">Address</span><span class="text-truncate"><?= htmlspecialchars($e['address']) ?></span></div>
              <?php endif; ?>
            </div>
            <div class="card-footer">
              <span class="source-tag source-tag-<?= htmlspecialchars($e['enquiry_source']) ?>">
                <i class="bi bi-<?= $e['enquiry_source'] === 'whatsapp' ? 'whatsapp' : 'globe' ?> me-1"></i><?= ucfirst(htmlspecialchars($e['enquiry_source'] ?? 'web')) ?>
              </span>
              <div class="d-flex gap-1">
                <button class="btn btn-outline-primary-custom btn-action-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?= (int)$e['id'] ?>" title="View Details"><i class="bi bi-eye"></i></button>
                <div class="btn-group">
                  <button class="btn btn-outline-secondary btn-action-sm dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <form method="post" class="d-inline">
                        <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="status" value="contacted">
                        <button type="submit" class="dropdown-item"><i class="bi bi-telephone me-2"></i>Mark Contacted</button>
                      </form>
                    </li>
                    <li>
                      <form method="post" class="d-inline">
                        <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="status" value="enrolled">
                        <button type="submit" class="dropdown-item"><i class="bi bi-check-circle me-2"></i>Mark Enrolled</button>
                      </form>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                      <form method="post" class="d-inline" onsubmit="return confirm('Delete this enrollment record?')">
                        <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
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

        <div class="modal fade" id="viewModal<?= (int)$e['id'] ?>" tabindex="-1">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" style="color:#1e293b;">Enrollment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-md-4 text-center mb-3">
                    <?php if (!empty($e['photo_data']) && !empty($e['photo_mime'])): ?>
                      <img src="photo.php?id=<?= (int)$e['id'] ?>" alt="" style="width:120px;height:120px;border-radius:50%;object-fit:cover;background:#e9ecef;">
                    <?php else: ?>
                      <img src="https://ui-avatars.com/api/?name=<?= urlencode($e['full_name']) ?>&background=d8000d&color=fff&size=120" alt="" style="width:120px;height:120px;border-radius:50%;">
                    <?php endif; ?>
                    <div class="mt-2">
                      <span class="badge-status <?= getStatusBadge($e['status']) ?>"><?= ucfirst(htmlspecialchars($e['status'])) ?></span>
                    </div>
                  </div>
                  <div class="col-md-8">
                    <table class="table table-sm table-borderless" style="font-size:0.88rem;">
                      <tr><td class="text-muted" style="width:120px;color:#64748b;">Full Name</td><td style="color:#1e293b;"><strong><?= htmlspecialchars($e['full_name']) ?></strong></td></tr>
                      <tr><td class="text-muted" style="color:#64748b;">Email</td><td style="color:#1e293b;"><?= htmlspecialchars($e['email']) ?></td></tr>
                      <tr><td class="text-muted" style="color:#64748b;">Phone</td><td style="color:#1e293b;"><?= htmlspecialchars($e['phone']) ?></td></tr>
                      <tr><td class="text-muted" style="color:#64748b;">Date of Birth</td><td style="color:#1e293b;"><?= $e['dob'] ? htmlspecialchars($e['dob']) : '-' ?></td></tr>
                      <tr><td class="text-muted" style="color:#64748b;">Course</td><td style="color:#1e293b;"><?= htmlspecialchars($e['course_name']) ?></td></tr>
                      <tr><td class="text-muted" style="color:#64748b;">Education</td><td style="color:#1e293b;"><?= htmlspecialchars($e['education'] ?? '-') ?></td></tr>
                      <tr><td class="text-muted" style="color:#64748b;">Source</td><td style="color:#1e293b;"><?= ucfirst(htmlspecialchars($e['enquiry_source'] ?? 'web')) ?></td></tr>
                      <tr><td class="text-muted" style="color:#64748b;">Submitted</td><td style="color:#1e293b;"><?= date('Y-m-d h:i A', strtotime($e['created_at'])) ?></td></tr>
                      <tr><td class="text-muted" style="color:#64748b;">IP Address</td><td style="color:#1e293b;" class="small"><?= htmlspecialchars($e['ip_address'] ?? '-') ?></td></tr>
                    </table>
                  </div>
                </div>
                <?php if (!empty($e['address'])): ?>
                  <div class="mt-2 pt-2 border-top" style="border-color:#f1f5f9 !important;">
                    <small style="color:#64748b;">Address:</small>
                    <p class="mb-0" style="color:#1e293b;"><?= nl2br(htmlspecialchars($e['address'])) ?></p>
                  </div>
                <?php endif; ?>
              </div>
              <div class="modal-footer">
                <form method="post" class="d-inline">
                  <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                  <input type="hidden" name="action" value="update_status">
                  <input type="hidden" name="status" value="contacted">
                  <button type="submit" class="btn btn-sm" style="background:#cce5ff;border-color:#b8daff;color:#004085;border-radius:8px;"><i class="bi bi-telephone me-1"></i>Mark Contacted</button>
                </form>
                <form method="post" class="d-inline">
                  <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                  <input type="hidden" name="action" value="update_status">
                  <input type="hidden" name="status" value="enrolled">
                  <button type="submit" class="btn btn-sm" style="background:#d4edda;border-color:#c3e6cb;color:#155724;border-radius:8px;"><i class="bi bi-check-circle me-1"></i>Mark Enrolled</button>
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
          Showing <?= $offset + 1 ?>–<?= min($offset + $per_page, $total_records) ?> of <?= $total_records ?> enrollment<?= $total_records !== 1 ? 's' : '' ?>
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
        Showing all <?= $total_records ?> enrollment<?= $total_records !== 1 ? 's' : '' ?>
      </div>
    <?php endif; ?>

  <?php else: ?>
    <div class="empty-state">
      <i class="bi bi-inbox"></i>
      <h5>No enrollments found</h5>
      <p>Try adjusting your search or filter criteria.</p>
      <?php if ($search || $status_filter || $course_filter): ?>
        <a href="index" class="btn btn-outline-primary-custom btn-sm rounded-pill px-3"><i class="bi bi-arrow-counterclockwise me-1"></i>Clear Filters</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>