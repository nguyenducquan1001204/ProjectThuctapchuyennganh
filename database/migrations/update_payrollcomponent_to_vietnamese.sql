-- ============================================
-- BƯỚC 1: Đổi cấu trúc bảng từ ENUM sang VARCHAR
-- ============================================
-- Thay đổi cột componentgroup từ ENUM sang VARCHAR
ALTER TABLE `payrollcomponent` 
MODIFY COLUMN `componentgroup` VARCHAR(50) NOT NULL;

-- Thay đổi cột calculationmethod từ ENUM sang VARCHAR
ALTER TABLE `payrollcomponent` 
MODIFY COLUMN `calculationmethod` VARCHAR(50) NOT NULL;

-- ============================================
-- BƯỚC 2: UPDATE dữ liệu từ tiếng Anh sang tiếng Việt
-- ============================================

-- UPDATE componentgroup: income → Thu nhập
UPDATE `payrollcomponent` 
SET `componentgroup` = 'Thu nhập' 
WHERE `componentgroup` = 'income';

-- UPDATE componentgroup: employee_deduction → Khoản trừ nhân viên
UPDATE `payrollcomponent` 
SET `componentgroup` = 'Khoản trừ nhân viên' 
WHERE `componentgroup` = 'employee_deduction';

-- UPDATE componentgroup: employer_contribution → Đóng góp đơn vị
UPDATE `payrollcomponent` 
SET `componentgroup` = 'Đóng góp đơn vị' 
WHERE `componentgroup` = 'employer_contribution';

-- UPDATE calculationmethod: fixed → Cố định
UPDATE `payrollcomponent` 
SET `calculationmethod` = 'Cố định' 
WHERE `calculationmethod` = 'fixed';

-- UPDATE calculationmethod: coefficient → Hệ số
UPDATE `payrollcomponent` 
SET `calculationmethod` = 'Hệ số' 
WHERE `calculationmethod` = 'coefficient';

-- UPDATE calculationmethod: percentage → Phần trăm
UPDATE `payrollcomponent` 
SET `calculationmethod` = 'Phần trăm' 
WHERE `calculationmethod` = 'percentage';

-- ============================================
-- BƯỚC 3: INSERT dữ liệu mới (nếu chưa insert)
-- ============================================
INSERT INTO `payrollcomponent` (
  `componentname`,
  `componentgroup`,
  `calculationmethod`,
  `componentdescription`
) VALUES
-- === NHÓM THU NHẬP ===
('Lương ngạch bậc', 'Thu nhập', 'Hệ số',
 'Hệ số lương ngạch bậc của giáo viên, nhân với mức lương cơ sở để ra tiền lương cơ bản.'),
('Phụ cấp chức vụ', 'Thu nhập', 'Hệ số',
 'Phụ cấp chức vụ lãnh đạo (Hiệu trưởng, Phó Hiệu trưởng, tổ trưởng...), tính theo hệ số trên lương cơ sở.'),
('Phụ cấp vượt khung', 'Thu nhập', 'Hệ số',
 'Phụ cấp vượt khung cho các trường hợp hưởng lương vượt bậc, hệ số vượt khung.'),
('Phụ cấp trách nhiệm', 'Thu nhập', 'Hệ số',
 'Phụ cấp trách nhiệm (Tổng phụ trách Đội, tổ trưởng, kiêm nhiệm...), tính theo hệ số trên lương cơ sở.'),
('Phụ cấp độc hại', 'Thu nhập', 'Hệ số',
 'Phụ cấp độc hại, nguy hiểm cho người làm việc trong môi trường có yếu tố độc hại, tính theo hệ số.'),
('Phụ cấp ưu đãi 30%', 'Thu nhập', 'Phần trăm',
 'Phụ cấp ưu đãi nhà giáo (30%) tính trên tiền lương dùng làm căn cứ (thường là lương ngạch bậc).'),
('Phụ cấp thâm niên', 'Thu nhập', 'Phần trăm',
 'Phụ cấp thâm niên nhà giáo, tính theo % trên lương ngạch bậc theo số năm công tác.'),
('Phụ cấp thể dục', 'Thu nhập', 'Hệ số',
 'Phụ cấp cho giáo viên dạy môn Thể dục hoặc công việc có tính chất đặc thù, tính theo hệ số.'),

-- === NHÓM KHOẢN TRỪ NHÂN VIÊN ===
('BHXH nhân viên 8%', 'Khoản trừ nhân viên', 'Phần trăm',
 'Khoản trích bảo hiểm xã hội phần người lao động phải đóng: 8% trên quỹ lương làm căn cứ BHXH.'),
('BHYT nhân viên 1.5%', 'Khoản trừ nhân viên', 'Phần trăm',
 'Khoản trích bảo hiểm y tế phần người lao động phải đóng: 1.5% trên quỹ lương làm căn cứ BHYT.'),
('BHTN nhân viên 1%', 'Khoản trừ nhân viên', 'Phần trăm',
 'Khoản trích bảo hiểm thất nghiệp phần người lao động phải đóng: 1% trên quỹ lương làm căn cứ BHTN.'),

-- === NHÓM KHOẢN ĐÓNG GÓP ĐƠN VỊ ===
('BHXH đơn vị 17%', 'Đóng góp đơn vị', 'Phần trăm',
 'Khoản bảo hiểm xã hội 17% do ngân sách/đơn vị đóng trên quỹ lương làm căn cứ BHXH.'),
('BHTN từ ngân sách 0.5%', 'Đóng góp đơn vị', 'Phần trăm',
 'Khoản 0.5% bảo hiểm thất nghiệp phần ngân sách hỗ trợ (theo cột 0.5 BHTN NLĐ trong file).'),
('BHYT đơn vị 3%', 'Đóng góp đơn vị', 'Phần trăm',
 'Khoản bảo hiểm y tế 3% do đơn vị/NSNN đóng trên quỹ lương làm căn cứ BHYT.'),
('BHTN đơn vị 1%', 'Đóng góp đơn vị', 'Phần trăm',
 'Khoản bảo hiểm thất nghiệp 1% do đơn vị/NSNN đóng trên quỹ lương làm căn cứ BHTN.');

