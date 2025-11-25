import pandas as pd
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from datetime import datetime

# --- CONFIGURATION ---
SMTP_SERVER = 'smtp.brownbi.com'  # e.g., smtp.secureserver.net
SMTP_PORT = 465  # or 587 for TLS
SMTP_USER = 'info@brownbi.com'
SMTP_PASS = 'nomaYO67GDDY!'
FROM_EMAIL = SMTP_USER
SUBJECT_DEFAULT = 'Hello from BBI'
EXCEL_FILE = '/Users/russellbrown/Library/CloudStorage/OneDrive-Personal/brownBI/bbiClients.xlsx'  # Path to your Excel file

# --- EMAIL BODY TEMPLATE ---
EMAIL_BODY = '''
Hi {name},

As {org}'s new CDIO, you're scaling AI—my Bedrock demo flags risks from notes in <4 weeks.

I’m offering a **$5,000, 4-week pilot** to deploy AI on patient notes.

Live demo: https://brownbi.com/healthcare-chatbot?name={first_name}

Reply to book.

Best regards,
Russ Brown
Brown Business Intelligence LLP
'''

def send_email(to_email, subject, body):
    msg = MIMEMultipart()
    msg['From'] = FROM_EMAIL
    msg['To'] = to_email
    msg['Subject'] = subject
    msg.attach(MIMEText(body, 'plain'))
    try:
        with smtplib.SMTP_SSL(SMTP_SERVER, SMTP_PORT) as server:
            server.login(SMTP_USER, SMTP_PASS)
            server.sendmail(FROM_EMAIL, to_email, msg.as_string())
        print(f"Email sent to {to_email}")
    except Exception as e:
        print(f"Failed to send email to {to_email}: {e}")

def main():
    df = pd.read_excel(EXCEL_FILE)
    for idx, row in df.iterrows():
        if str(row.get('skip', 0)) == '1':
            continue  # Skip this row if skip == 1
        name = row.get('Name', '')
        first_name = name.split()[0] if name else ''
        org = row.get('Organization', '')  # New variable for organization
        to_email = row.get('Email', '')  # Use actual email from Excel
        subject = row.get('Subject', SUBJECT_DEFAULT)
        body = EMAIL_BODY.format(name=name, first_name=first_name, org=org)
        send_email(to_email, su        <div style="background:rgba(249,249,249,0.3);padding:20px;border-radius:12px;">bject, body)
        df.at[idx, 'last_touch'] = str(datetime.now().strftime('%Y-%m-%d %H:%M:%S'))  # Cast to string
        mails_sent = row.get('mails_sent', 0)
        try:
            mails_sent = int(mails_sent)
        except (ValueError, TypeError):
            mails_sent = 0
        df.at[idx, 'mails_sent'] = mails_sent + 1  # Increment mails_sent
    df.to_excel(EXCEL_FILE, index=False)  # Save changes

if __name__ == '__main__':
    main()
