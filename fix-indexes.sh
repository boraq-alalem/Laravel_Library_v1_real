#!/bin/bash

# إصلاح فهرس العنوان الطويل
echo "إصلاح فهرس العنوان..."

mysql -u alalem -p123456789@Rc AlalemLibrary -e "
ALTER TABLE theses ADD INDEX idx_title_short (title(255));
ALTER TABLE theses ADD INDEX idx_degree_year (degree_id, year);
ALTER TABLE theses ADD INDEX idx_uni_spec (university_id, specialization_id);
ALTER TABLE theses ADD INDEX idx_author_year (author_id, year);
ALTER TABLE theses ADD INDEX idx_created (created_at);

ALTER TABLE authors ADD INDEX idx_author_name (name);
ALTER TABLE universities ADD INDEX idx_university_name (name);
ALTER TABLE specializations ADD INDEX idx_specialization_name (name);
" 2>/dev/null || echo "بعض الفهارس موجودة مسبقاً"

echo "تم إصلاح الفهارس!"