#!/bin/bash

echo "=== بدء ضغط جميع ملفات PDF الكبيرة ==="

# إحصائيات
total_files=0
compressed_files=0
failed_files=0
total_saved=0

# معالجة الملفات على دفعات
find storage/app/public/pdfs -name "*.pdf" -size +1M | while read file; do
    if [ -f "$file" ]; then
        size_before=$(stat -c%s "$file" 2>/dev/null || echo 0)
        
        # تخطي الملفات الصغيرة جداً
        if [ $size_before -lt 500000 ]; then
            continue
        fi
        
        echo "معالجة: $(basename "$file") ($((size_before/1024))KB)"
        
        # ضغط الملف
        gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook \
           -dNOPAUSE -dQUIET -dBATCH -sOutputFile="${file}.tmp" "$file" 2>/dev/null
        
        if [ -f "${file}.tmp" ] && [ -s "${file}.tmp" ]; then
            size_after=$(stat -c%s "${file}.tmp" 2>/dev/null || echo 0)
            
            # استبدال إذا كان التوفير أكثر من 10%
            if [ $size_after -lt $((size_before * 90 / 100)) ]; then
                mv "${file}.tmp" "$file"
                saved=$((size_before - size_after))
                total_saved=$((total_saved + saved))
                echo "✅ ضُغط: توفير $((saved/1024))KB ($(((size_before-size_after)*100/size_before))%)"
                ((compressed_files++))
            else
                rm -f "${file}.tmp"
                echo "⚠️ تخطي: لا توجد فائدة كافية"
            fi
        else
            rm -f "${file}.tmp"
            echo "❌ فشل الضغط"
            ((failed_files++))
        fi
        
        ((total_files++))
        
        # استراحة كل 50 ملف
        if [ $((total_files % 50)) -eq 0 ]; then
            echo "--- تم معالجة $total_files ملف ---"
            sleep 2
        fi
    fi
done

echo ""
echo "=== تقرير الضغط النهائي ==="
echo "إجمالي الملفات المعالجة: $total_files"
echo "الملفات المضغوطة: $compressed_files"
echo "الملفات الفاشلة: $failed_files"
echo "إجمالي المساحة الموفرة: $((total_saved/1024/1024))MB"
echo "=== انتهى ضغط ملفات PDF ==="