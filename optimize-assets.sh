#!/bin/bash

echo "تحسين الأصول..."

# ضغط الصور
find public -name "*.jpg" -o -name "*.jpeg" | xargs jpegoptim --max=85 --strip-all
find public -name "*.png" | xargs optipng -o2

# ضغط CSS و JS
find public -name "*.css" | xargs gzip -k
find public -name "*.js" | xargs gzip -k

echo "تم تحسين الأصول"