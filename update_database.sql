-- تحديث جدول entreprises لإضافة is_archived
ALTER TABLE entreprises ADD COLUMN is_archived BOOLEAN DEFAULT FALSE;
ALTER TABLE entreprises ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
