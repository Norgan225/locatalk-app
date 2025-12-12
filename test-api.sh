#!/bin/bash

# ========================================
# Script de Test API - LocaTalk
# ========================================

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
API_URL="http://localhost:8000/api"
EMAIL="marie@apec.com"
PASSWORD="password123"
MAC_ADDRESS="00:11:22:33:44:55"

# Compteurs
TESTS_PASSED=0
TESTS_FAILED=0

# Fonction pour afficher un test
test_endpoint() {
    local test_name=$1
    local response=$2
    local expected=$3

    if echo "$response" | grep -q "$expected"; then
        echo -e "${GREEN}✅ $test_name${NC}"
        ((TESTS_PASSED++))
        return 0
    else
        echo -e "${RED}❌ $test_name${NC}"
        echo -e "${YELLOW}   Response: ${response:0:200}...${NC}"
        ((TESTS_FAILED++))
        return 1
    fi
}

echo -e "${BLUE}"
echo "╔════════════════════════════════════════╗"
echo "║     🧪 Tests API LocaTalk            ║"
echo "╔════════════════════════════════════════╗"
echo -e "${NC}"
echo ""

# ========================================
# 1. AUTHENTIFICATION
# ========================================
echo -e "${BLUE}━━━ 1️⃣  AUTHENTIFICATION ━━━${NC}"
echo ""

echo "Login..."
LOGIN_RESPONSE=$(curl -s -X POST "$API_URL/login" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"email\": \"$EMAIL\",
    \"password\": \"$PASSWORD\",
    \"device_name\": \"test-script\",
    \"mac_address\": \"$MAC_ADDRESS\"
  }")

TOKEN=$(echo $LOGIN_RESPONSE | grep -o '"token":"[^"]*' | sed 's/"token":"//')

if [ -z "$TOKEN" ]; then
    echo -e "${RED}❌ Login échoué - Impossible de continuer${NC}"
    echo "$LOGIN_RESPONSE"
    exit 1
fi

test_endpoint "Login" "$LOGIN_RESPONSE" "token"
echo -e "   ${GREEN}Token: ${TOKEN:0:30}...${NC}"
echo ""

echo "Me (utilisateur connecté)..."
ME_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/me" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Me" "$ME_RESPONSE" "email"
echo ""

# ========================================
# 2. DASHBOARD
# ========================================
echo -e "${BLUE}━━━ 2️⃣  DASHBOARD ━━━${NC}"
echo ""

echo "Dashboard principal..."
DASHBOARD_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/dashboard" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Dashboard" "$DASHBOARD_RESPONSE" "personal"
echo ""

echo "Analytics..."
ANALYTICS_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/dashboard/analytics" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Analytics" "$ANALYTICS_RESPONSE" "users_growth"
echo ""

# ========================================
# 3. PROFILE
# ========================================
echo -e "${BLUE}━━━ 3️⃣  PROFILE ━━━${NC}"
echo ""

echo "Voir profil..."
PROFILE_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/profile" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Profile" "$PROFILE_RESPONSE" "email"
echo ""

echo "Liste des appareils..."
DEVICES_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/profile/devices" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Devices" "$DEVICES_RESPONSE" "data"
echo ""

# ========================================
# 4. USERS
# ========================================
echo -e "${BLUE}━━━ 4️⃣  USERS ━━━${NC}"
echo ""

echo "Liste des utilisateurs..."
USERS_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/users" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Users List" "$USERS_RESPONSE" "data"
echo ""

# ========================================
# 5. ORGANIZATIONS
# ========================================
echo -e "${BLUE}━━━ 5️⃣  ORGANIZATIONS ━━━${NC}"
echo ""

echo "Liste des organisations..."
ORGS_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/organizations" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Organizations List" "$ORGS_RESPONSE" "data"
echo ""

# ========================================
# 6. DEPARTMENTS
# ========================================
echo -e "${BLUE}━━━ 6️⃣  DEPARTMENTS ━━━${NC}"
echo ""

echo "Liste des départements..."
DEPTS_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/departments" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Departments List" "$DEPTS_RESPONSE" "data"
echo ""

# ========================================
# 7. PROJECTS
# ========================================
echo -e "${BLUE}━━━ 7️⃣  PROJECTS ━━━${NC}"
echo ""

echo "Liste des projets..."
PROJECTS_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/projects" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Projects List" "$PROJECTS_RESPONSE" "data"
echo ""

# ========================================
# 8. TASKS
# ========================================
echo -e "${BLUE}━━━ 8️⃣  TASKS ━━━${NC}"
echo ""

echo "Mes tâches..."
MYTASKS_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/tasks/my-tasks" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "My Tasks" "$MYTASKS_RESPONSE" "data"
echo ""

echo "Liste des tâches..."
TASKS_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/tasks" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Tasks List" "$TASKS_RESPONSE" "data"
echo ""

# ========================================
# 9. MESSAGES
# ========================================
echo -e "${BLUE}━━━ 9️⃣  MESSAGES ━━━${NC}"
echo ""

echo "Conversations..."
CONVERSATIONS_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/messages/conversations" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Conversations" "$CONVERSATIONS_RESPONSE" "data"
echo ""

echo "Nombre de messages non lus..."
UNREAD_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/messages/unread-count" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Unread Count" "$UNREAD_RESPONSE" "count"
echo ""

# ========================================
# 10. CHANNELS
# ========================================
echo -e "${BLUE}━━━ 🔟  CHANNELS ━━━${NC}"
echo ""

echo "Liste des canaux..."
CHANNELS_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/channels" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Channels List" "$CHANNELS_RESPONSE" "data"
echo ""

# ========================================
# 11. CALLS
# ========================================
echo -e "${BLUE}━━━ 1️⃣1️⃣  CALLS ━━━${NC}"
echo ""

echo "Liste des appels..."
CALLS_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/calls?my_calls=true" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Calls List" "$CALLS_RESPONSE" "data"
echo ""

echo "Historique des appels..."
CALL_HISTORY_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/calls/history" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Call History" "$CALL_HISTORY_RESPONSE" "data"
echo ""

# ========================================
# 12. MEETINGS
# ========================================
echo -e "${BLUE}━━━ 1️⃣2️⃣  MEETINGS ━━━${NC}"
echo ""

echo "Liste des réunions..."
MEETINGS_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/meetings" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Meetings List" "$MEETINGS_RESPONSE" "data"
echo ""

# ========================================
# 13. NOTIFICATIONS
# ========================================
echo -e "${BLUE}━━━ 1️⃣3️⃣  NOTIFICATIONS ━━━${NC}"
echo ""

echo "Liste des notifications..."
NOTIFS_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/notifications" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Notifications List" "$NOTIFS_RESPONSE" "data"
echo ""

echo "Nombre de notifications non lues..."
NOTIFS_COUNT_RESPONSE=$(curl -s -H "Accept: application/json" "$API_URL/notifications/unread-count" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Notifications Count" "$NOTIFS_COUNT_RESPONSE" "count"
echo ""

# ========================================
# 14. LOGOUT
# ========================================
echo -e "${BLUE}━━━ 1️⃣4️⃣  LOGOUT ━━━${NC}"
echo ""

echo "Logout..."
LOGOUT_RESPONSE=$(curl -s -X POST "$API_URL/logout" \
  -H "Authorization: Bearer $TOKEN" \
  -H "X-Mac-Address: $MAC_ADDRESS")
test_endpoint "Logout" "$LOGOUT_RESPONSE" "message"
echo ""

# ========================================
# RÉSUMÉ
# ========================================
echo -e "${BLUE}"
echo "╔════════════════════════════════════════╗"
echo "║          📊 RÉSUMÉ DES TESTS         ║"
echo "╚════════════════════════════════════════╝"
echo -e "${NC}"
echo ""

TOTAL_TESTS=$((TESTS_PASSED + TESTS_FAILED))
SUCCESS_RATE=$(( TESTS_PASSED * 100 / TOTAL_TESTS ))

echo -e "Total de tests    : ${BLUE}$TOTAL_TESTS${NC}"
echo -e "Tests réussis     : ${GREEN}$TESTS_PASSED${NC}"
echo -e "Tests échoués     : ${RED}$TESTS_FAILED${NC}"
echo -e "Taux de réussite  : ${YELLOW}$SUCCESS_RATE%${NC}"
echo ""

if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "${GREEN}╔════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║  🎉 TOUS LES TESTS SONT PASSÉS ! 🎉  ║${NC}"
    echo -e "${GREEN}╚════════════════════════════════════════╝${NC}"
    exit 0
else
    echo -e "${RED}╔════════════════════════════════════════╗${NC}"
    echo -e "${RED}║  ⚠️  CERTAINS TESTS ONT ÉCHOUÉ  ⚠️   ║${NC}"
    echo -e "${RED}╚════════════════════════════════════════╝${NC}"
    exit 1
fi
