<?php

$file = 'app/Http/Controllers/Api/StatsController.php';
$content = file_get_contents($file);

// إصلاح latestTheses - السطر 148
$content = str_replace(
    'return response()->json($result->values());
    }

    public function searchTheses',
    'return response()->json([
            "data" => $result->values(),
            "pagination" => [
                "current_page" => $page,
                "per_page" => $perPage,
                "total" => $total,
                "last_page" => ceil($total / $perPage),
                "from" => ($page - 1) * $perPage + 1,
                "to" => min($page * $perPage, $total)
            ]
        ]);
        });
    }

    public function searchTheses',
    $content
);

file_put_contents($file, $content);
echo "تم إصلاح pagination بنجاح!\n";