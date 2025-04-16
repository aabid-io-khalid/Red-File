<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Report - PELIXS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        h1, h2, h3 {
            color: #1a202c;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .section {
            margin-bottom: 20px;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .metric {
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 8px;
        }
        .metric h4 {
            margin: 0 0 10px;
            font-size: 16px;
            color: #4a5568;
        }
        .metric p {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f7fafc;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #718096;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>PELIXS Analytics Report</h1>
        <p>Generated on <?php echo e(now()->format('F j, Y')); ?> for the last <?php echo e($date_range); ?> days</p>

        <div class="section">
            <h2>Key Metrics</h2>
            <div class="metrics-grid">
                <div class="metric">
                    <h4>Total Users</h4>
                    <p><?php echo e(number_format($total_users)); ?></p>
                    <small>+<?php echo e(number_format($new_users_last_month)); ?> this month</small>
                </div>
                <div class="metric">
                    <h4>Total Subscriptions</h4>
                    <p><?php echo e(number_format($total_subscriptions)); ?></p>
                    <small>+<?php echo e(number_format($new_subscriptions_last_month)); ?> this month</small>
                </div>
                <div class="metric">
                    <h4>Total Revenue</h4>
                    <p>$<?php echo e(number_format($total_subscription_revenue, 2)); ?></p>
                    <small><?php echo e(number_format($revenue_growth, 1)); ?>% vs last month</small>
                </div>
                <div class="metric">
                    <h4>Total Comments</h4>
                    <p><?php echo e(number_format($total_comments)); ?></p>
                    <small>+<?php echo e(number_format($new_comments_last_month)); ?> this month</small>
                </div>
                <div class="metric">
                    <h4>Average Comment Rating</h4>
                    <p><?php echo e(number_format($avg_comment_rating, 1)); ?> / 5</p>
                </div>
                <div class="metric">
                    <h4>Content Distribution</h4>
                    <p>Movies: <?php echo e(number_format($content_distribution['movies'])); ?></p>
                    <p>Series: <?php echo e(number_format($content_distribution['series'])); ?></p>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>Most Popular Movies</h2>
            <?php if(empty($most_watched_movies)): ?>
                <p>No movie data available.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Popularity</th>
                            <th>Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $most_watched_movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($movie['title']); ?></td>
                                <td><?php echo e(number_format($movie['popularity'], 1)); ?></td>
                                <td><?php echo e(number_format($movie['vote_average'], 1)); ?>/10</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>Genre Distribution</h2>
            <?php if(empty($genre_distribution)): ?>
                <p>No genre data available.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Genre</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $genre_distribution; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($genre['name']); ?></td>
                                <td><?php echo e(number_format($genre['count'])); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>Generated by PELIXS Admin Dashboard</p>
        </div>
    </div>
</body>
</html><?php /**PATH C:\Users\Youcode\Herd\file-rouge\resources\views/admin/reports.blade.php ENDPATH**/ ?>