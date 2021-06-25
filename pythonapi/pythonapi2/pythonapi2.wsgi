activate_this = '/var/www/pythonapi2/venv/bin/activate_this.py'
execfile(activate_this, dict(__file__=activate_this))
import sys
sys.path.insert(0, '/var/www/pythonapi2/')
from api import app as application