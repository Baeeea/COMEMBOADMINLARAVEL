<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Image Test</title>
    <style>
        body {
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #2563eb;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .image-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .image-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #3b82f6;
        }
        pre {
            background: #f1f5f9;
            padding: 10px;
            border-radius: 4px;
            overflow: auto;
        }
        .info {
            background-color: #e0f2fe;
            border-left: 4px solid #0284c7;
            padding: 10px 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h1>Profile Image Test</h1>
    
    <div class="card">
        <h2>New Laravel Route Test</h2>
        <div class="info">
            <p>This page tests the new Laravel route for profile images. It compares both the old API endpoint and the new route.</p>
        </div>
        
        <?php
        // Bootstrap the Laravel application
        require __DIR__.'/../vendor/autoload.php';
        $app = require_once __DIR__.'/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        
        use App\Models\User;
        
        // Get all users with profile images
        $users = User::whereNotNull('profile')->get();
        
        if ($users->isEmpty()) {
            echo '<div class="info">No users with profile images found.</div>';
        } else {
            echo '<h3>Found ' . $users->count() . ' users with profile images</h3>';
            echo '<div class="grid">';
            
            foreach ($users as $user) {
                $id = $user->id;
                $name = $user->name;
                
                // Generate URLs for both methods
                $oldApiUrl = url("/api/profile_image.php?id={$id}&t=".time());
                $newRouteUrl = route('profile.image', ['id' => $id, 'v' => time()]);
                
                echo '<div class="image-container">';
                echo "<h4>{$name}</h4>";
                
                echo '<div style="margin-bottom: 10px;">';
                echo '<h5>New Route</h5>';
                echo "<img src='{$newRouteUrl}' class='image-preview' alt='User Profile'>";
                echo '</div>';
                
                echo '<div>';
                echo '<h5>Old API</h5>';
                echo "<img src='{$oldApiUrl}' class='image-preview' alt='User Profile'>";
                echo '</div>';
                
                echo '</div>';
            }
            
            echo '</div>';
        }
        ?>
        
        <h3>URL Examples</h3>
        <pre>
// Old API URL:
/api/profile_image.php?id=1&t=<?= time() ?>

// New Laravel Route:
<?= route('profile.image', ['id' => 1, 'v' => time()]) ?>
        </pre>
        
        <h3>Usage in Blade Templates</h3>
        <pre>
&lt;!-- Old way --&gt;
&lt;img src="{{ Auth::user()->profile ? url('/api/profile_image.php?id='.Auth::user()->id.'&t='.time()) : 'fallback-url' }}" alt="Profile"&gt;

&lt;!-- New way --&gt;
&lt;img src="{{ Auth::user()->profile ? route('profile.image', ['id' => Auth::user()->id, 'v' => time()]) : 'fallback-url' }}" alt="Profile"&gt;

&lt;!-- Using the getAvatarUrl method --&gt;
&lt;img src="{{ Auth::user()->getAvatarUrl() }}" alt="Profile"&gt;
        </pre>
    </div>
</body>
</html>
