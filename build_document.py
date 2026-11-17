from docx import Document
from docx.shared import Pt
from docx.enum.text import WD_ALIGN_PARAGRAPH

output_path = r'C:\xampp\htdocs\hotel_maintainance_department\Hotel_Maintenance_Documentation.docx'
doc = Document()
style = doc.styles['Normal']
style.font.name = 'Times New Roman'
style.font.size = Pt(12)
style.paragraph_format.line_spacing = 1.5
style.paragraph_format.space_after = Pt(6)

p = doc.add_paragraph()
p.alignment = WD_ALIGN_PARAGRAPH.CENTER
r = p.add_run('MAISON HOTEL OPERATIONS AND MAINTENANCE MANAGEMENT SYSTEM')
r.bold = True
r.font.size = Pt(18)

p = doc.add_paragraph()
p.alignment = WD_ALIGN_PARAGRAPH.CENTER
r = p.add_run('Project Documentation')
r.italic = True

sections = [
    ('Overview', 'The Maison Hotel Operations and Maintenance Management System is a full-stack web application designed to simplify room and facility monitoring in a hotel environment. It centralizes daily operations like room status tracking, cleaning, inspections, maintenance requests, work orders, and inventory control.'),
    ('System Purpose', 'The project was created to replace fragmented manual record keeping with a single digital operations hub. Hotel staff can assign tasks, track service progress, and maintain a clear timeline of issues that affect guest comfort and property upkeep.'),
    ('Modules', 'The system includes rooms, cleaning tasks, inspections, maintenance requests, work orders, repairs, equipment, inventory, staff records, schedules, lost and found, and summary reporting.'),
    ('Technology Stack', 'The solution uses PHP, MySQL, and a responsive front-end interface. The architecture supports secure session handling, role-based access, and organized operational workflows for both administrators and staff.'),
    ('Operational Value', 'By improving visibility and accountability, the platform supports faster response times, clearer communication, and better maintenance planning across the property. This allows hotel teams to manage work more efficiently and keep operations running smoothly.')
]
for title, content in sections:
    doc.add_heading(title, level=1)
    doc.add_paragraph(content)

p = doc.add_paragraph()
p.alignment = WD_ALIGN_PARAGRAPH.CENTER
r = p.add_run('Prepared for Hotel Maintenance Operations')
r.font.size = Pt(11)

doc.save(output_path)
print(output_path)
