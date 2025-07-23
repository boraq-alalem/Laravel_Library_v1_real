#!/bin/bash

echo "ضغط ملفات PDF..."

# البحث عن ملفات PDF وضغطها
find storage/app/public/pdfs -name "*.pdf" -size +1M | while read file; do
    echo "ضغط: $file"
    gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=/ebook \
       -dNOPAUSE -dQUIET -dBATCH -sOutputFile="${file}.compressed" "$file"
    
    if [ -f "${file}.compressed" ]; then
        mv "${file}.compressed" "$file"
        echo "تم ضغط: $file"
    fi
done

echo "انتهى ضغط ملفات PDF"