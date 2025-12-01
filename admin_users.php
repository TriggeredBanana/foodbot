<?php
// Admin page for listing and deleting users

session_start();

require_once __DIR__ . '/includes/autoloader.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/database.php';

// Helper for showing messages same way as auth.php
function admin_message($key, $class)
{
    if (!empty($_SESSION[$key])) {
        echo '<div class="' . $class . '">' . htmlspecialchars($_SESSION[$key]) . '</div>';
        unset($_SESSION[$key]);
    }
}

// Require login
if (!isset($_SESSION['username'])) {
    $_SESSION['error'] = "You must be logged in to access that page.";
    header("Location: auth.php");
    exit;
}

$page_title = 'FoodBot - Admin - Users';

// Handle delete request (POST → Redirect → GET)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $deleteId = filter_input(INPUT_POST, 'delete_user_id', FILTER_VALIDATE_INT);

    if ($deleteId) {
        try {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$deleteId]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['admin_success'] = "User with ID $deleteId was deleted.";
            } else {
                $_SESSION['admin_error'] = "No user found with that ID.";
            }
        } catch (PDOException $e) {
            $_SESSION['admin_error'] = "Database error while deleting user.";
        }
    } else {
        $_SESSION['admin_error'] = "Invalid user ID.";
    }

    header("Location: admin_users.php");
    exit;
}

// Fetch all users
try {
    $stmt = $conn->query("SELECT id, username, usertype, created_at FROM users ORDER BY id ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
    $_SESSION['admin_error'] = "Could not load users.";
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="chat-container">
    <h2 style="color:#333;">Manage users (admin)<br><br></h2>

    <?php admin_message('admin_success', 'info-message'); ?>
    <?php admin_message('admin_error', 'error-message'); ?>

    <?php if (empty($users)): ?>
    <p>No users found.</p>
    <?php else: ?>
    <table style="width:100%; border-collapse:collapse; background:#fff;">
        <thead>
            <tr style="background:#f2f2f2;">
                <th style="padding:8px; border:1px solid #ddd;">ID</th>
                <th style="padding:8px; border:1px solid #ddd;">Username</th>
                <th style="padding:8px; border:1px solid #ddd;">Usertype</th>
                <th style="padding:8px; border:1px solid #ddd;">Created</th>
                <th style="padding:8px; border:1px solid #ddd; text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < count($users); $i++): ?>
            <?php $u = $users[$i]; ?>
            <tr>
                <td style="padding:8px; border:1px solid #ddd;"><?php echo $u['id']; ?></td>
                <td style="padding:8px; border:1px solid #ddd;"><?php echo htmlspecialchars($u['username']); ?></td>
                <td style="padding:8px; border:1px solid #ddd;"><?php echo htmlspecialchars($u['usertype']); ?></td>
                <td style="padding:8px; border:1px solid #ddd;"><?php echo htmlspecialchars($u['created_at']); ?></td>
                <td style="padding:8px; border:1px solid #ddd; text-align:center;">
                    <form method="post" onsubmit="return confirm('Delete this user?');">
                        <input type="hidden" name="delete_user_id" value="<?php echo $u['id']; ?>">
                        <button type="submit"
                            style="padding:6px 12px; border:none; border-radius:5px; background:#c0392b; color:#fff;">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>