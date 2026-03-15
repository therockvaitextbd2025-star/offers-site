<?php
// Force the browser to fetch new data each time
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once 'engine.php'; 
$userId = htmlspecialchars($_GET['user_id'] ?? '', ENT_QUOTES);
$offers = !empty($userId) ? load_offers($userId) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AXiRON Offerwall</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 15px; }
        .container { width: 90%; max-width: 600px; margin: auto; }
        .offer-card { 
            background: #fff; border-radius: 12px; padding: 12px; margin-bottom: 15px; 
            display: flex; align-items: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-left: 5px solid #28a745; 
        }
        /* কমপ্লিট হওয়া অফারের স্টাইল */
        .offer-card.completed { border-left: 5px solid #999; opacity: 0.7; }
        
        .offer-card img { width: 60px; height: 60px; border-radius: 10px; object-fit: cover; margin-right: 15px; }
        .offer-details { flex-grow: 1; }
        .offer-details h3 { margin: 0; font-size: 16px; color: #333; }
        .offer-details p { margin: 4px 0; font-size: 13px; color: #666; }
        
        .reward { font-weight: bold; color: #28a745; font-size: 14px; }
        
        .btn-start { 
            background: #28a745; color: #fff; text-decoration: none; padding: 8px 15px; 
            border-radius: 8px; font-size: 13px; font-weight: bold; transition: 0.3s;
            border: none; cursor: pointer;
        }
        .btn-start:hover { background: #218838; }
        /* কমপ্লিট বাটন স্টাইল */
        .btn-completed { background: #999; cursor: not-allowed; }
        
        .no-offer { text-align: center; color: #999; margin-top: 50px; }
    </style>
</head>
<body>

<div class="container">
    <?php if (empty($userId)): ?>
        <p class="no-offer">Please login to see offers.</p>
    <?php elseif (empty($offers)): ?>
        <p class="no-offer">No offers available for your location right now.</p>
    <?php else: ?>
        <?php foreach ($offers as $o): ?>
            <div class="offer-card <?php echo ($o['is_completed'] ?? false) ? 'completed' : ''; ?>">
                <img src="<?php echo htmlspecialchars($o['image']); ?>" alt="Icon" onerror="this.src='https://via.placeholder.com/60'">
                <div class="offer-details">
                    <h3><?php echo htmlspecialchars($o['title']); ?></h3>
                    <p>Task: <?php echo htmlspecialchars($o['task_type']); ?></p>
                    <p class="reward">Status: <?php echo ($o['is_completed'] ?? false) ? 'Completed' : 'Available'; ?></p>
                </div>
                
                <?php if ($o['is_completed'] ?? false): ?>
                    <button class="btn-start btn-completed" disabled>Completed</button>
                <?php else: ?>
                    <a href="<?php echo htmlspecialchars($o['link']); ?>" target="_blank" class="btn-start">Start</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
