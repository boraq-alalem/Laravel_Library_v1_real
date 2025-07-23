#!/bin/bash

echo "بدء ضغط ملفات PDF..."

# إحصائيات قبل الضغط
total_files=0
compressed_files=0
total_size_before=0
total_size_after=0

# البحث عن ملفات PDF الكبيرة (أكبر من 1MB)
find storage/app/public/pdfs -name "*.pdf" -size +1M | while read file; do
    if [ -f "$file" ]; then
        # حساب الحجم قبل الضغط
        size_before=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file" 2>/dev/null || echo 0)
        
        echo "ضغط: $(basename "$file") - الحجم: $((size_before/1024))KB"
        
        # ضغط الملف
        gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook \
           -dNOPAUSE -dQUIET -dBATCH -sOutputFile="${file}.tmp" "$file" 2>/dev/null
        
        # فحص نجاح الضغط
        if [ -f "${file}.tmp" ] && [ -s "${file}.tmp" ]; then
            size_after=$(stat -f%z "${file}.tmp" 2>/dev/null || stat -c%s "${file}.tmp" 2>/dev/null || echo 0)
            
            # استبدال الملف إذا كان الضغط مفيد (توفير أكثر من 10%)
            if [ $size_after -lt $((size_before * 90 / 100)) ]; then
                mv "${file}.tmp" "$file"
                echo "✅ تم ضغط $(basename "$file") من $((size_before/1024))KB إلى $((size_after/1024))KB"
                ((compressed_files++))
            else
                rm -f "${file}.tmp"
                echo "⚠️ لم يتم ضغط $(basename "$file") - لا توجد فائدة"
            fi
        else
            rm -f "${file}.tmp"
            echo "❌ فشل ضغط $(basename "$file")"
        fi
        
        ((total_files++))
        
        # توقف كل 10 ملفات لتجنب الحمل الزائد
        if [ $((total_files % 10)) -eq 0 ]; then
            echo "تم معالجة $total_files ملف..."
            sleep 1
        fi
    fi
done

echo "انتهى ضغط ملفات PDF"
echo "تم معالجة $total_files ملف"
echo "تم ضغط $compressed_files ملف بنجاح"