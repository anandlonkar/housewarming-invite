import os
import json
import ftplib
import getpass
from ftplib import FTP

CONFIG_FILE = 'ftp_config.json'

def load_config():
    if os.path.exists(CONFIG_FILE):
        with open(CONFIG_FILE, 'r') as f:
            return json.load(f)
    return {}

def get_credentials(config):
    host = config.get('host') or input("FTP Host: ")
    user = config.get('user') or input("FTP Username: ")
    password = config.get('password') or getpass.getpass("FTP Password: ")
    remote_path = config.get('remote_path') or input("Remote Path (default: public_html): ") or "public_html"
    return host, user, password, remote_path

def upload_files(ftp, local_path, remote_path):
    for root, dirs, files in os.walk(local_path):
        # Ignore .git and other hidden folders/files and specific excluded files
        dirs[:] = [d for d in dirs if not d.startswith('.') and d != '__pycache__']
        files[:] = [f for f in files if not f.startswith('.') and f != 'deploy.py' and f != 'ftp_config.json' and f != 'ftp_config.json.example' and not f.endswith('.pyc')]

        rel_path = os.path.relpath(root, local_path)
        if rel_path == '.':
            current_remote_dir = remote_path
        else:
            current_remote_dir = os.path.join(remote_path, rel_path).replace("\\", "/")

        # Create remote directory if it doesn't exist
        try:
            ftp.mkd(current_remote_dir)
            print(f"Created directory: {current_remote_dir}")
        except ftplib.error_perm:
            pass # Directory likely exists

        for file in files:
            local_file_path = os.path.join(root, file)
            remote_file_path = os.path.join(current_remote_dir, file).replace("\\", "/")
            
            print(f"Uploading {local_file_path} to {remote_file_path}...")
            with open(local_file_path, 'rb') as f:
                ftp.storbinary(f'STOR {remote_file_path}', f)

def main():
    print("--- Housewarming Invite FTP Deployer ---")
    
    config = load_config()
    host, user, password, remote_path = get_credentials(config)

    try:
        print(f"Connecting to {host}...")
        ftp = FTP(host)
        ftp.login(user, password)
        print("Connected successfully.")

        upload_files(ftp, '.', remote_path)

        print("Deployment complete!")
        ftp.quit()
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    main()
