#!/bin/bash

echo "اختبار ضغط عينة من ملفات PDF..."

# اختبار على 5 ملفات فقط
find storage/app/public/pdfs -name "*.pdf" -size +1M | head -5 | while read file; do
    if [ -f "$file" ]; then
        size_before=$(stat -c%s "$file" 2>/dev/null || echo 0)
        echo "اختبار: $(basename "$file") - الحجم: $((size_before/1024))KB"
        
        gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook \
           -dNOPAUSE -dQUIET -dBATCH -sOutputFile="${file}.test" "$file" 2>/dev/null
        
        if [ -f "${file}.test" ]; then
            size_after=$(stat -c%s "${file}.test" 2>/dev/null || echo 0)
            savings=$((100 - (size_after * 100 / size_before)))
            echo "✅ النتيجة: $((size_before/1024))KB → $((size_after/1024))KB (توفير: ${savings}%)"
            rm -f "${file}.test"
        else
            echo "❌ فشل الاختبار"
        fi
    fi
done

echo "انتهى اختبار العينة"