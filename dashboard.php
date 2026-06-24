<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FinTrack 2.0</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>
    <div class="background-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="auth-header" style="margin-bottom: 20px;">
                <h1 class="logo" style="font-size: 1.5rem;">Fin<span>Track</span></h1>
            </div>
            
            <nav class="sidebar-nav">
                <a href="#" class="nav-item active" onclick="showSection('overview')"><i class="fas fa-home"></i> Overview</a>
                <a href="#" class="nav-item" onclick="showSection('budget')"><i class="fas fa-wallet"></i> Budget Planner</a>
                <a href="#" class="nav-item" onclick="showSection('debts')"><i class="fas fa-hand-holding-usd"></i> Debt Payoff</a>
                <a href="#" class="nav-item" onclick="showSection('wealth')"><i class="fas fa-gem"></i> Wealth Tracker</a>
                <a href="#" class="nav-item" onclick="showSection('investments')"><i class="fas fa-chart-line"></i> Investments</a>
                <a href="#" class="nav-item" onclick="showSection('reports')"><i class="fas fa-file-pdf"></i> Reports</a>
                <a href="#" class="nav-item" onclick="showSection('converter')"><i class="fas fa-coins"></i> Currency</a>
                <a href="profile.php" class="nav-item"><i class="fas fa-user-circle"></i> Profile</a>
            </nav>

            <div class="sidebar-footer">
                <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <main class="content-area">
            <!-- Overview Section -->
            <section id="overview" class="dashboard-section active">
                <header class="section-header">
                    <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                    <p>Here's your financial snapshot for today.</p>
                </header>

                <div class="card-grid">
                    <div class="feature-card" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(0, 242, 254, 0.1)); border: 1px solid var(--primary);">
                        <h3>Net Worth</h3>
                        <div class="stat-value" id="overview-networth" style="color: var(--primary);">₹ 0.00</div>
                        <p>Assets &minus; Liabilities</p>
                    </div>
                    <div class="feature-card">
                        <h3>Quick Budget</h3>
                        <div class="stat-value" id="overview-income">₹ 0.00</div>
                        <p>Total Monthly Income</p>
                    </div>
                    <div class="feature-card">
                        <h3>Top Recommendation</h3>
                        <div class="stat-value" style="font-size: 1.2rem; color: var(--primary);">Gold & SIP</div>
                        <p>Diversification is key</p>
                    </div>
                    <div class="feature-card" style="background: rgba(138, 43, 226, 0.05); border: 1px solid rgba(138, 43, 226, 0.3);">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <h3>Level <span id="user-level">1</span></h3>
                            <span style="color: var(--text-muted); font-size: 0.8rem;"><span id="user-xp">0</span> XP</span>
                        </div>
                        <div style="height: 10px; background: rgba(0,0,0,0.3); border-radius: 5px; margin: 15px 0; overflow: hidden;">
                            <div id="xp-progress" style="height: 100%; width: 0%; background: linear-gradient(to right, #8a2be2, #00f2fe); transition: width 0.5s ease;"></div>
                        </div>
                        <p style="font-size: 0.8rem; color: var(--text-muted);">Next Level at <span id="next-level-xp">100</span> XP</p>
                    </div>
                    <div class="feature-card" id="upcoming-bills-card">
                        <h3>Upcoming Bills</h3>
                        <div id="upcoming-bills-list" style="margin-top: 15px;">
                            <p style="color: var(--text-muted); font-size: 0.85rem;">No upcoming bills.</p>
                        </div>
                    </div>
                    <div class="feature-card" style="background: rgba(0, 242, 254, 0.05); border: 1px solid var(--primary);">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                            <i class="fas fa-robot" style="color: var(--primary);"></i>
                            <h3>AI Financial Advisor</h3>
                        </div>
                        <div id="ai-tips-container" style="font-size: 0.9rem; line-height: 1.5; color: var(--text);">
                            <p style="color: var(--text-muted);">Analyzing your finances...</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Budget Section -->
            <section id="budget" class="dashboard-section" style="display:none;">
                <header class="section-header">
                    <h2>Smart Budget Planner</h2>
                    <p>Enter income in any currency (e.g. $2000, 5000 AED) to plan your budget.</p>
                </header>

                <div class="feature-card" style="margin-bottom: 20px;">
                    <div style="display: flex; gap: 15px; align-items: flex-start; flex-wrap: wrap;">
                        <div class="input-group" style="flex: 1; min-width: 300px;">
                            <i class="fas fa-search-dollar"></i>
                            <input type="text" id="budget-income-input" placeholder="Type income e.g. '$2500' or '150000'..." oninput="analyzeAndBudget()">
                        </div>
                        <div class="symbol-grid" style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <button class="symbol-btn" onclick="prefillBudgetSymbol('$')">$</button>
                            <button class="symbol-btn" onclick="prefillBudgetSymbol('€')">€</button>
                            <button class="symbol-btn" onclick="prefillBudgetSymbol('£')">£</button>
                            <button class="symbol-btn" onclick="prefillBudgetSymbol('¥')">¥</button>
                            <button class="symbol-btn" onclick="prefillBudgetSymbol('AED')">AED</button>
                            <button class="symbol-btn" onclick="prefillBudgetSymbol('₹')">₹</button>
                        </div>
                    </div>
                    <div id="budget-detection-pill" style="display: none; margin: 15px 0;">
                        <span style="background: var(--secondary); color: #000; padding: 5px 12px; border-radius: 20px; font-weight: 700; font-size: 0.75rem;" id="budget-detected-label"></span>
                    </div>
                </div>

                <!-- Targets removed as per user request -->


                <div class="card-grid" style="margin-top: 30px; grid-template-columns: 1fr;">
                    <!-- Actual Needs Tracking -->
                    <div class="feature-card">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h3>Your Actual Needs</h3>
                            <button class="primary-btn" style="width: auto; padding: 8px 15px; font-size: 0.8rem;" onclick="addRow('needs')">+ Add Need</button>
                        </div>
                        <div id="needs-list" class="expense-list">
                            <!-- Rows will be added here -->
                        </div>
                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--glass-border); display: flex; justify-content: space-between;">
                            <span>Total Spent:</span>
                            <strong id="actual-needs-total">₹ 0.00</strong>
                        </div>
                    </div>

                    <!-- Budget Distribution Pie Chart -->
                    <div class="feature-card" style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <h3 style="margin-bottom: 20px;">Budget Distribution</h3>
                        <div style="width: 250px; height: 250px;">
                            <canvas id="budgetPieChart"></canvas>
                        </div>
                        <div id="pie-legend" style="margin-top: 20px; font-size: 0.85rem; color: var(--text-muted);">
                            <!-- Legend will be here -->
                        </div>
                    </div>

                    <!-- Actual Wants Tracking -->
                    <div class="feature-card">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h3>Your Actual Wants</h3>
                            <div style="display:flex; gap:10px;">
                                <button class="primary-btn" style="width: auto; padding: 8px 15px; font-size: 0.8rem;" onclick="addRow('wants')">+ Add Want</button>
                                <button class="primary-btn" style="width: auto; padding: 8px 15px; font-size: 0.8rem; background: linear-gradient(to right,#ff0070,#7000ff);" onclick="toggleGoalPlanner()">🎯 Plan a Goal</button>
                            </div>
                        </div>
                        <div id="wants-list" class="expense-list">
                            <!-- Rows will be added here -->
                        </div>
                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--glass-border); display: flex; justify-content: space-between;">
                            <span>Total Spent:</span>
                            <strong id="actual-wants-total">₹ 0.00</strong>
                        </div>

                        <!-- Goal Purchase Planner -->
                        <div id="goal-planner" style="display:none; margin-top:20px; padding:20px; background: rgba(112,0,255,0.08); border:1px solid rgba(112,0,255,0.3); border-radius:15px;">
                            <h4 style="color:#a78bfa; margin-bottom:15px;">🏍️ Big Purchase Goal Planner</h4>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                                <div class="input-group">
                                    <i class="fas fa-tag"></i>
                                    <input type="text" id="goal-name" placeholder="e.g. Bike, iPhone..." oninput="calculateGoal()">
                                </div>
                                <div class="input-group">
                                    <i class="fas fa-rupee-sign"></i>
                                    <input type="number" id="goal-price" placeholder="Total Price (₹)" oninput="calculateGoal()">
                                </div>
                            </div>
                            <div id="goal-result" style="display:none;">
                                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:15px;">
                                    <div style="background:rgba(255,255,255,0.05); border-radius:12px; padding:15px; text-align:center;">
                                        <p style="color:var(--text-muted); font-size:0.8rem; margin-bottom:5px;">Monthly Allocation</p>
                                        <div style="font-size:1.3rem; font-weight:800; color:#a78bfa;" id="goal-monthly-alloc">₹ 0</div>
                                        <p style="font-size:0.7rem; color:var(--text-muted); margin-top:4px;">from your savings</p>
                                    </div>
                                    <div style="background:rgba(255,255,255,0.05); border-radius:12px; padding:15px; text-align:center;">
                                        <p style="color:var(--text-muted); font-size:0.8rem; margin-bottom:5px;">Months Needed</p>
                                        <div style="font-size:1.3rem; font-weight:800; color:#00f2fe;" id="goal-months">—</div>
                                        <p style="font-size:0.7rem; color:var(--text-muted); margin-top:4px;">to save up</p>
                                    </div>
                                    <div style="background:rgba(255,255,255,0.05); border-radius:12px; padding:15px; text-align:center;">
                                        <p style="color:var(--text-muted); font-size:0.8rem; margin-bottom:5px;">Ready By</p>
                                        <div style="font-size:1.1rem; font-weight:800; color:#10b981;" id="goal-date">—</div>
                                        <p style="font-size:0.7rem; color:var(--text-muted); margin-top:4px;">estimated month</p>
                                    </div>
                                </div>
                                <p id="goal-status-msg" style="margin-top:15px; font-size:0.85rem; color:var(--text-muted); text-align:center;"></p>
                            </div>
                            <p id="goal-no-income" style="color:#ff4b4b; font-size:0.85rem; text-align:center;">⚠️ Please enter your income first to calculate goal months.</p>
                        </div>
                    </div>
                </div>

                <!-- Actual Savings Result -->
                <div class="feature-card" style="margin-top: 30px; border: 2px solid #10b981; background: rgba(16,185,129,0.05);" id="actual-savings-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                        <div>
                            <h3 style="color: #10b981; margin-bottom: 8px;">💰 Actual Savings</h3>
                            <p style="color: var(--text-muted); font-size: 0.85rem;">Income &minus; Needs &minus; Wants</p>
                        </div>
                        <div style="text-align: right;">
                            <div class="stat-value" id="actual-savings" style="color: #10b981;">₹ 0.00</div>
                            <p id="savings-status" style="font-size: 0.8rem; margin-top: 5px;">Enter your income &amp; expenses above</p>
                        </div>
                    </div>
                </div>

                <!-- Monthly Growth Chart -->
                <div class="feature-card" style="margin-top: 30px;" id="savings-chart-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <h3>📈 Monthly Savings Growth</h3>
                            <p style="color: var(--text-muted); font-size: 0.85rem;">How your savings accumulate over 12 months</p>
                        </div>
                        <div id="annual-total-badge" style="display:none; background: linear-gradient(to right, var(--primary), var(--secondary)); color:#000; padding: 10px 20px; border-radius: 20px; font-weight: 800; font-size: 1rem;">
                            Annual Total: <span id="annual-total-value">₹ 0</span>
                        </div>
                    </div>
                    <canvas id="savingsChart" height="100"></canvas>
                </div>
            </section>

            <!-- Bills Section -->
            <section id="bills" class="dashboard-section" style="display:none;">
                <header class="section-header">
                    <h2>Bill Reminders</h2>
                    <p>Track your recurring payments and never miss a due date.</p>
                </header>

                <div class="feature-card" style="margin-bottom: 25px;">
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1.5fr 1fr auto; gap: 15px; align-items: flex-end;">
                        <div class="input-group" style="margin-bottom:0;">
                            <i class="fas fa-tag"></i>
                            <input type="text" id="bill-name" placeholder="Bill Name (e.g. Rent)">
                        </div>
                        <div class="input-group" style="margin-bottom:0;">
                            <i class="fas fa-rupee-sign"></i>
                            <input type="number" id="bill-amount" placeholder="Amount">
                        </div>
                        <div class="input-group" style="margin-bottom:0;">
                            <i class="fas fa-calendar"></i>
                            <input type="date" id="bill-date" class="date-input">
                        </div>
                        <select id="bill-recurring" class="row-input">
                            <option value="none">One-time</option>
                            <option value="monthly">Monthly</option>
                            <option value="weekly">Weekly</option>
                        </select>
                        <button class="primary-btn" style="width: auto; padding: 12px 25px;" onclick="saveBill()">Add Bill</button>
                    </div>
                </div>

                <div class="feature-card">
                    <h3>Your Bills</h3>
                    <div id="full-bills-list" style="margin-top: 20px;">
                        <!-- Bills will be listed here -->
                    </div>
                </div>
            </section>

            <!-- Investments Section -->
            <section id="investments" class="dashboard-section" style="display:none;">
                <header class="section-header">
                    <h2>Investment Calculator</h2>
                    <p>Calculate your projected returns over time.</p>
                </header>

                <div class="feature-card" style="margin-bottom: 30px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="input-group">
                            <i class="fas fa-wallet"></i>
                            <input type="number" id="invest-amount" placeholder="Investment Amount (₹)" oninput="calculateReturns()">
                        </div>
                        <div class="input-group">
                            <i class="fas fa-calendar-alt"></i>
                            <input type="number" id="invest-years" placeholder="Number of Years" oninput="calculateReturns()">
                        </div>
                    </div>
                </div>

                <div class="card-grid">
                    <div class="feature-card" style="border-top: 4px solid gold;">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <i class="fas fa-coins" style="font-size: 2rem; color: gold;"></i>
                            <h3>Gold (Avg. 10%)</h3>
                        </div>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 10px;">Lumpsum Investment</p>
                        <div class="stat-value" id="gold-profit" style="color: gold;">₹ 0.00</div>
                        <p id="gold-total-val" style="font-size: 0.8rem; opacity: 0.7;">Projected Value: ₹ 0.00</p>
                    </div>
                    
                    <div class="feature-card" style="border-top: 4px solid #4facfe;">
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                            <i class="fas fa-piggy-bank" style="font-size: 2rem; color: #4facfe;"></i>
                            <h3>SIP (Avg. 15%)</h3>
                        </div>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 10px;">Monthly SIP Projection</p>
                        <div class="stat-value" id="sip-profit" style="color: #4facfe;">₹ 0.00</div>
                        <p id="sip-total-val" style="font-size: 0.8rem; opacity: 0.7;">Projected Value: ₹ 0.00</p>
                    </div>
                </div>

                <!-- Investment Growth Chart -->
                <div class="feature-card" style="margin-top: 30px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <h3>📊 Investment Growth Over Time</h3>
                            <p style="color: var(--text-muted); font-size: 0.85rem;">Year-by-year comparison of Gold vs SIP returns</p>
                        </div>
                        <div id="invest-final-badge" style="display:none; background: linear-gradient(to right, gold, #f59e0b); color:#000; padding: 10px 20px; border-radius: 20px; font-weight: 800; font-size: 0.9rem;">
                            Best at Year <span id="invest-best-year">—</span>: <span id="invest-best-val">₹ 0</span>
                        </div>
                    </div>
                    <canvas id="investChart" height="110"></canvas>
                </div>
            </section>

            <!-- Debts Section -->
            <section id="debts" class="dashboard-section" style="display:none;">
                <header class="section-header">
                    <h2>Debt Payoff Strategist</h2>
                    <p>Compare Snowball (smallest first) vs Avalanche (highest interest first) methods.</p>
                </header>

                <div class="feature-card" style="margin-bottom: 25px;">
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 15px; align-items: flex-end;">
                        <div class="input-group" style="margin-bottom:0;">
                            <i class="fas fa-credit-card"></i>
                            <input type="text" id="debt-name" placeholder="Debt Name (e.g. Credit Card)">
                        </div>
                        <div class="input-group" style="margin-bottom:0;">
                            <i class="fas fa-rupee-sign"></i>
                            <input type="number" id="debt-amount" placeholder="Total Balance">
                        </div>
                        <div class="input-group" style="margin-bottom:0;">
                            <i class="fas fa-percentage"></i>
                            <input type="number" id="debt-rate" placeholder="Interest %">
                        </div>
                        <div class="input-group" style="margin-bottom:0;">
                            <i class="fas fa-money-bill-wave"></i>
                            <input type="number" id="debt-min" placeholder="Min. Payment">
                        </div>
                        <button class="primary-btn" style="width: auto; padding: 12px 25px;" onclick="saveDebt()">Add Debt</button>
                    </div>
                </div>

                <div class="card-grid">
                    <div class="feature-card" style="border-left: 5px solid #ff4b4b;">
                        <h3 style="color: #ff4b4b;">Snowball Method</h3>
                        <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom:15px;">Focus on smallest balance first for psychological wins.</p>
                        <div id="snowball-result" class="stat-value" style="font-size: 1.5rem;">—</div>
                        <p id="snowball-summary" style="font-size: 0.85rem; color: var(--text-muted);"></p>
                    </div>
                    <div class="feature-card" style="border-left: 5px solid #10b981;">
                        <h3 style="color: #10b981;">Avalanche Method</h3>
                        <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom:15px;">Focus on highest interest first to save money.</p>
                        <div id="avalanche-result" class="stat-value" style="font-size: 1.5rem;">—</div>
                        <p id="avalanche-summary" style="font-size: 0.85rem; color: var(--text-muted);"></p>
                    </div>
                </div>

                <div class="feature-card" style="margin-top: 25px;">
                    <h3>Debt List</h3>
                    <div id="debts-list" style="margin-top: 15px;">
                        <!-- Debts listed here -->
                    </div>
                </div>
            </section>

            <!-- Wealth Section -->
            <section id="wealth" class="dashboard-section" style="display:none;">
                <header class="section-header">
                    <h2>Wealth Tracker</h2>
                    <p>Track your assets to see your true net worth grow over time.</p>
                </header>

                <div class="feature-card" style="margin-bottom: 25px;">
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 15px; align-items: flex-end;">
                        <div class="input-group" style="margin-bottom:0;">
                            <i class="fas fa-box"></i>
                            <input type="text" id="asset-name" placeholder="Asset Name (e.g. Savings, Property)">
                        </div>
                        <select id="asset-type" class="row-input">
                            <option value="Cash">Cash / Bank</option>
                            <option value="Investment">Investment</option>
                            <option value="Property">Property</option>
                            <option value="Gold">Gold</option>
                            <option value="Crypto">Crypto</option>
                        </select>
                        <div class="input-group" style="margin-bottom:0;">
                            <i class="fas fa-rupee-sign"></i>
                            <input type="number" id="asset-value" placeholder="Current Value">
                        </div>
                        <button class="primary-btn" style="width: auto; padding: 12px 25px;" onclick="saveAsset()">Add Asset</button>
                    </div>
                </div>

                <div class="card-grid">
                    <div class="feature-card">
                        <h3>Total Assets</h3>
                        <div id="total-assets-val" class="stat-value" style="color: #10b981;">₹ 0</div>
                    </div>
                    <div class="feature-card">
                        <h3>Total Liabilities</h3>
                        <div id="total-liabilities-val" class="stat-value" style="color: #ff4b4b;">₹ 0</div>
                    </div>
                </div>

                <div class="feature-card" style="margin-top: 25px;">
                    <h3>Assets List</h3>
                    <div id="assets-list" style="margin-top: 15px;">
                        <!-- Assets listed here -->
                    </div>
                </div>
            </section>

            <!-- Reports Section -->
            <section id="reports" class="dashboard-section" style="display:none;">
                <header class="section-header">
                    <h2>Financial Reports</h2>
                    <p>Export your monthly financial summary to PDF or CSV.</p>
                </header>

                <div class="card-grid">
                    <div class="feature-card" style="text-align: center; padding: 40px;">
                        <i class="fas fa-file-pdf" style="font-size: 3rem; color: #ff4b4b; margin-bottom: 20px;"></i>
                        <h3>PDF Summary</h3>
                        <p style="color: var(--text-muted); margin-bottom: 25px;">Detailed breakdown of income, expenses, debts, and net worth.</p>
                        <button class="primary-btn" onclick="exportToPDF()">Download PDF Report</button>
                    </div>
                    <div class="feature-card" style="text-align: center; padding: 40px;">
                        <i class="fas fa-file-csv" style="font-size: 3rem; color: #10b981; margin-bottom: 20px;"></i>
                        <h3>CSV Transactions</h3>
                        <p style="color: var(--text-muted); margin-bottom: 25px;">Export all transaction history to a spreadsheet compatible format.</p>
                        <button class="primary-btn" style="background: #10b981;" onclick="exportToCSV()">Download CSV</button>
                    </div>
                </div>
            </section>

            <!-- Converter Section -->
            <section id="converter" class="dashboard-section" style="display:none;">
                <header class="section-header">
                    <h2>Smart Currency Analyzer</h2>
                    <p>Type an amount with a symbol (e.g. $100, €50, 1000 JPY) to convert to INR.</p>
                </header>

                <div class="feature-card">
                    <div style="display: flex; gap: 15px; align-items: flex-start; flex-wrap: wrap;">
                        <div class="input-group" style="flex: 1; min-width: 300px;">
                            <i class="fas fa-search"></i>
                            <input type="text" id="smart-input" placeholder="Try typing '$100' or '50 EUR'..." oninput="analyzeAndConvert()">
                        </div>
                        <div class="symbol-grid" style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <button class="symbol-btn" onclick="prefillSymbol('$')">$</button>
                            <button class="symbol-btn" onclick="prefillSymbol('€')">€</button>
                            <button class="symbol-btn" onclick="prefillSymbol('£')">£</button>
                            <button class="symbol-btn" onclick="prefillSymbol('¥')">¥</button>
                            <button class="symbol-btn" onclick="prefillSymbol('AED')">AED</button>
                        </div>
                    </div>
                    <div id="detection-pill" style="display: none; margin: 15px 0;">
                        <span style="background: var(--primary); color: #000; padding: 5px 15px; border-radius: 20px; font-weight: 700; font-size: 0.8rem;" id="detected-currency-label"></span>
                    </div>
                    <div class="stat-value" id="conversion-result" style="text-align: center; margin-top: 20px;">₹ 0.00</div>
                    <p id="rate-info" style="text-align: center; color: var(--text-muted); font-size: 0.8rem;"></p>
                </div>
            </section>
        </main>
    </div>

    <script>
        function showSection(sectionId) {
            document.querySelectorAll('.dashboard-section').forEach(s => s.style.display = 'none');
            document.getElementById(sectionId).style.display = 'block';
            
            document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
            if (event) event.currentTarget.classList.add('active');
        }

        // ========================
        // PERSISTENCE ENGINE
        // ========================
        window.addEventListener('load', loadUserData);
        window._currentData = null;

        async function loadUserData() {
            try {
                const response = await fetch('api.php?action=get_data');
                const data = await response.json();
                window._currentData = data;

                if (data.settings) {
                    const input = document.getElementById('budget-income-input');
                    input.value = (data.settings.currency === 'INR' ? '₹' : data.settings.currency) + data.settings.monthly_income;
                    analyzeAndBudget();
                }

                if (data.stats) {
                    updateGamificationUI(data.stats);
                }

                if (data.bills) {
                    renderBills(data.bills);
                }

                if (data.debts) {
                    renderDebts(data.debts);
                    calculatePayoffStrategies(data.debts);
                }

                if (data.assets) {
                    renderAssets(data.assets, data.debts || []);
                }

                if (data.transactions) {
                    data.transactions.forEach(t => {
                        addPersistedRow(t.type, t);
                    });
                    runAIAdvisor(data);
                }
                
                calculateActualTotal('needs');
                calculateActualTotal('wants');
                updateActualSavings();
            } catch (e) {
                console.error("Failed to load data", e);
            }
        }

        async function saveTransaction(type, rowData) {
            try {
                const response = await fetch('api.php?action=add_transaction', {
                    method: 'POST',
                    body: JSON.stringify({ type, ...rowData })
                });
                return await response.json();
            } catch (e) {
                console.error("Failed to save transaction", e);
            }
        }

        async function deleteTransaction(id) {
            try {
                await fetch('api.php?action=delete_transaction', {
                    method: 'POST',
                    body: JSON.stringify({ id })
                });
            } catch (e) {
                console.error("Failed to delete transaction", e);
            }
        }

        function updateGamificationUI(stats) {
            const level = Math.floor(stats.xp / 100) + 1;
            const xpInLevel = stats.xp % 100;
            const nextLevelXp = 100;
            const progress = (xpInLevel / nextLevelXp) * 100;

            document.getElementById('user-level').innerText = level;
            document.getElementById('user-xp').innerText = stats.xp;
            document.getElementById('xp-progress').style.width = progress + '%';
            document.getElementById('next-level-xp').innerText = nextLevelXp;
        }

        async function saveBill() {
            const name = document.getElementById('bill-name').value;
            const amount = document.getElementById('bill-amount').value;
            const due_date = document.getElementById('bill-date').value;
            const recurring = document.getElementById('bill-recurring').value;

            if (name && amount && due_date) {
                const res = await fetch('api.php?action=save_bill', {
                    method: 'POST',
                    body: JSON.stringify({ name, amount, due_date, recurring })
                });
                const data = await res.json();
                if (data.success) {
                    location.reload(); // Quick refresh to update lists
                }
            }
        }

        function renderBills(bills) {
            const overviewList = document.getElementById('upcoming-bills-list');
            const fullList = document.getElementById('full-bills-list');
            
            overviewList.innerHTML = '';
            fullList.innerHTML = '';

            const today = new Date();
            const upcoming = bills.filter(b => !b.is_paid).slice(0, 3);

            if (upcoming.length === 0) {
                overviewList.innerHTML = '<p style="color: var(--text-muted); font-size: 0.85rem;">No upcoming bills.</p>';
            }

            upcoming.forEach(b => {
                const date = new Date(b.due_date);
                const diffTime = date - today;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                const urgencyColor = diffDays <= 3 ? '#ff4b4b' : diffDays <= 7 ? '#f59e0b' : '#10b981';

                overviewList.innerHTML += `
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div>
                            <div style="font-weight: 600; font-size: 0.9rem;">${b.name}</div>
                            <div style="font-size: 0.75rem; color: ${urgencyColor};">${diffDays < 0 ? 'Overdue!' : 'Due in ' + diffDays + ' days'}</div>
                        </div>
                        <div style="font-weight: 700; color: var(--primary);">₹${parseFloat(b.amount).toLocaleString()}</div>
                    </div>
                `;
            });

            bills.forEach(b => {
                fullList.innerHTML += `
                    <div class="expense-row" style="align-items: center; padding: 15px; background: rgba(255,255,255,0.03); border-radius: 15px; margin-bottom: 10px;">
                        <div style="flex: 2; font-weight: 600;">${b.name}</div>
                        <div style="flex: 1; color: var(--primary); font-weight: 700;">₹${parseFloat(b.amount).toLocaleString()}</div>
                        <div style="flex: 1.5; color: var(--text-muted); font-size: 0.85rem;">${b.due_date} (${b.recurring})</div>
                        <div style="flex: 1;">
                            ${b.is_paid ? 
                                '<span style="color: #10b981; font-size: 0.85rem;"><i class="fas fa-check-circle"></i> Paid</span>' : 
                                `<button class="primary-btn" style="padding: 8px 15px; font-size: 0.75rem;" onclick="payBill(${b.id})">Mark Paid</button>`}
                        </div>
                    </div>
                `;
            });
        }

        async function payBill(id) {
            const res = await fetch('api.php?action=pay_bill', {
                method: 'POST',
                body: JSON.stringify({ id })
            });
            const data = await res.json();
            if (data.success) {
                location.reload();
            }
        }

        async function saveAsset() {
            const name = document.getElementById('asset-name').value;
            const type = document.getElementById('asset-type').value;
            const value = document.getElementById('asset-value').value;

            if (name && type && value) {
                const res = await fetch('api.php?action=save_asset', {
                    method: 'POST',
                    body: JSON.stringify({ name, type, value })
                });
                const data = await res.json();
                if (data.success) {
                    location.reload();
                }
            }
        }

        function renderAssets(assets, debts) {
            const list = document.getElementById('assets-list');
            list.innerHTML = '';
            let totalAssets = 0;
            assets.forEach(a => {
                totalAssets += parseFloat(a.value);
                list.innerHTML += `
                    <div class="expense-row" style="align-items: center; padding: 15px; background: rgba(255,255,255,0.03); border-radius: 15px; margin-bottom: 10px;">
                        <div style="flex: 2; font-weight: 600;">${a.name}</div>
                        <div style="flex: 1; color: var(--text-muted); font-size: 0.85rem;">${a.type}</div>
                        <div style="flex: 1; color: #10b981; font-weight: 700;">₹${parseFloat(a.value).toLocaleString()}</div>
                    </div>
                `;
            });

            const totalLiabilities = debts.reduce((sum, d) => sum + parseFloat(d.total_amount), 0);
            const netWorth = totalAssets - totalLiabilities;

            document.getElementById('total-assets-val').innerText = '₹ ' + totalAssets.toLocaleString();
            document.getElementById('total-liabilities-val').innerText = '₹ ' + totalLiabilities.toLocaleString();
            document.getElementById('overview-networth').innerText = '₹ ' + netWorth.toLocaleString();
        }

        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const data = window._currentData;

            doc.setFontSize(22);
            doc.text("FinTrack 2.0 - Financial Report", 20, 20);
            
            doc.setFontSize(14);
            doc.text(`Generated on: ${new Date().toLocaleDateString()}`, 20, 30);
            doc.text(`User: ${document.querySelector('h2').innerText.split(',')[1].replace('!','')}`, 20, 40);

            doc.line(20, 45, 190, 45);

            let y = 60;
            doc.setFontSize(16);
            doc.text("Summary", 20, y);
            y += 10;
            doc.setFontSize(12);
            doc.text(`Monthly Income: ${document.getElementById('overview-income').innerText}`, 20, y);
            y += 7;
            doc.text(`Net Worth: ${document.getElementById('overview-networth').innerText}`, 20, y);
            y += 7;
            doc.text(`Current Level: ${document.getElementById('user-level').innerText}`, 20, y);

            y += 20;
            doc.setFontSize(16);
            doc.text("Top Transactions", 20, y);
            y += 10;
            doc.setFontSize(10);
            data.transactions.slice(0, 10).forEach(t => {
                doc.text(`${t.transaction_date} - ${t.name} (${t.category}): ₹${t.amount}`, 25, y);
                y += 7;
            });

            doc.save("FinTrack_Report.pdf");
        }

        function exportToCSV() {
            const data = window._currentData.transactions;
            let csv = "Date,Name,Amount,Type,Category\n";
            data.forEach(t => {
                csv += `${t.transaction_date},${t.name},${t.amount},${t.type},${t.category}\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.setAttribute('hidden', '');
            a.setAttribute('href', url);
            a.setAttribute('download', 'transactions.csv');
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        async function saveDebt() {
            const name = document.getElementById('debt-name').value;
            const total_amount = document.getElementById('debt-amount').value;
            const interest_rate = document.getElementById('debt-rate').value;
            const min_payment = document.getElementById('debt-min').value;

            if (name && total_amount && interest_rate && min_payment) {
                const res = await fetch('api.php?action=save_debt', {
                    method: 'POST',
                    body: JSON.stringify({ name, total_amount, interest_rate, min_payment })
                });
                const data = await res.json();
                if (data.success) {
                    location.reload();
                }
            }
        }

        function renderDebts(debts) {
            const list = document.getElementById('debts-list');
            list.innerHTML = '';
            debts.forEach(d => {
                list.innerHTML += `
                    <div class="expense-row" style="align-items: center; padding: 15px; background: rgba(255,255,255,0.03); border-radius: 15px; margin-bottom: 10px;">
                        <div style="flex: 2; font-weight: 600;">${d.name}</div>
                        <div style="flex: 1; color: #ff4b4b; font-weight: 700;">₹${parseFloat(d.total_amount).toLocaleString()}</div>
                        <div style="flex: 1; color: var(--text-muted); font-size: 0.85rem;">${d.interest_rate}% APR</div>
                        <div style="flex: 1; color: var(--text-muted); font-size: 0.85rem;">Min: ₹${parseFloat(d.min_payment).toLocaleString()}</div>
                    </div>
                `;
            });
        }

        function calculatePayoffStrategies(debts) {
            if (debts.length === 0) return;

            const extraPayment = 2000; // Assuming $2000 extra per month for now

            const snowball = [...debts].sort((a, b) => a.total_amount - b.total_amount);
            const avalanche = [...debts].sort((a, b) => b.interest_rate - a.interest_rate);

            const calc = (list) => {
                let months = 0;
                let totalPaid = 0;
                let currentBalances = list.map(d => ({ ...d, balance: parseFloat(d.total_amount) }));

                while (currentBalances.some(d => d.balance > 0) && months < 360) {
                    months++;
                    let monthlyExtra = extraPayment;
                    currentBalances.forEach(d => {
                        if (d.balance > 0) {
                            const interest = d.balance * (d.interest_rate / 100 / 12);
                            totalPaid += interest;
                            d.balance += interest;
                            
                            const pay = Math.min(d.balance, parseFloat(d.min_payment));
                            d.balance -= pay;
                            totalPaid += pay;
                        }
                    });

                    // Apply extra payment to first debt in prioritized list
                    for (let d of currentBalances) {
                        if (d.balance > 0) {
                            const pay = Math.min(d.balance, monthlyExtra);
                            d.balance -= pay;
                            totalPaid += pay;
                            monthlyExtra -= pay;
                            if (monthlyExtra <= 0) break;
                        }
                    }
                }
                return { months, totalPaid };
            };

            const sResult = calc(snowball);
            const aResult = calc(avalanche);

            document.getElementById('snowball-result').innerText = sResult.months + " Months";
            document.getElementById('snowball-summary').innerText = `Total Paid: ₹${sResult.totalPaid.toLocaleString()}`;

            document.getElementById('avalanche-result').innerText = aResult.months + " Months";
            document.getElementById('avalanche-summary').innerText = `Total Paid: ₹${aResult.totalPaid.toLocaleString()}`;
        }

        function runAIAdvisor(data) {
            const tips = [];
            const income = data.settings ? parseFloat(data.settings.monthly_income) : 0;
            const transactions = data.transactions || [];
            
            if (income === 0) {
                tips.push("Please set your monthly income in Profile to get personalized advice.");
            } else {
                const needs = transactions.filter(t => t.type === 'need').reduce((sum, t) => sum + parseFloat(t.amount), 0);
                const wants = transactions.filter(t => t.type === 'want').reduce((sum, t) => sum + parseFloat(t.amount), 0);
                const savings = income - needs - wants;

                // Rule 1: 50/30/20 check
                if (needs > income * 0.5) tips.push(`⚠️ Your <b>Needs</b> are ${((needs/income)*100).toFixed(0)}% of income. Try to keep it under 50%.`);
                if (wants > income * 0.3) tips.push(`⚠️ You're spending ${((wants/income)*100).toFixed(0)}% on <b>Wants</b>. Recommended is 30%.`);
                if (savings < income * 0.2) tips.push(`📉 Savings alert: You're only saving ${((savings/income)*100).toFixed(0)}%. Aim for 20%.`);

                // Rule 2: Bills check
                const unpaidBills = data.bills ? data.bills.filter(b => !b.is_paid).length : 0;
                if (unpaidBills > 0) tips.push(`🔔 You have <b>${unpaidBills} unpaid bills</b>. Pay them soon to avoid late fees.`);

                // Rule 3: Debt check
                if (data.debts && data.debts.length > 0) {
                    tips.push(`💡 Use the <b>Avalanche method</b> for your debts to save the most on interest!`);
                }

                if (tips.length === 0) tips.push("✅ Your finances look great! Keep up the disciplined tracking.");
            }

            const container = document.getElementById('ai-tips-container');
            container.innerHTML = tips.map(t => `<div style="margin-bottom: 10px;">${t}</div>`).join('');
        }

        async function analyzeAndBudget() {
            const input = document.getElementById('budget-income-input').value.trim();
            const pill = document.getElementById('budget-detection-pill');
            const pillLabel = document.getElementById('budget-detected-label');
            
            const needsEl = document.getElementById('budget-needs');
            const wantsEl = document.getElementById('budget-wants');
            const savingsEl = document.getElementById('budget-savings');
            const overviewIncome = document.getElementById('overview-income');

            if (!input) {
                if (needsEl) needsEl.innerText = '₹ 0.00';
                if (wantsEl) wantsEl.innerText = '₹ 0.00';
                if (savingsEl) savingsEl.innerText = '₹ 0.00';
                overviewIncome.innerText = '₹ 0.00';
                pill.style.display = 'none';
                return;
            }

            const map = {
                '$': 'usd', 'usd': 'usd',
                '€': 'eur', 'eur': 'eur',
                '£': 'gbp', 'gbp': 'gbp',
                '¥': 'jpy', 'jpy': 'jpy',
                'aed': 'aed', 'dh': 'aed',
                '₹': 'inr', 'inr': 'inr', 'rs': 'inr'
            };

            const amountMatch = input.match(/(\d+(\.\d+)?)/);
            const textParts = input.toLowerCase().match(/[$€£¥₹]|[a-z]{3}|[a-z]{2}/g) || [];
            
            let originalAmount = amountMatch ? parseFloat(amountMatch[0]) : 0;
            let detectedCurrency = 'inr'; // default for budget is often local

            // If a symbol/text is found, override default
            for (let part of textParts) {
                if (map[part]) {
                    detectedCurrency = map[part];
                    break;
                }
            }

            if (originalAmount === 0) {
                pill.style.display = 'none';
                return;
            }

            pill.style.display = 'block';
            pillLabel.innerText = `Detected: ${detectedCurrency.toUpperCase()} ${originalAmount}`;

            let incomeInInr = originalAmount;

            if (detectedCurrency !== 'inr') {
                try {
                    const response = await fetch(`https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/${detectedCurrency}.json`);
                    const data = await response.json();
                    const rate = data[detectedCurrency].inr;
                    incomeInInr = originalAmount * rate;
                } catch (error) {
                    console.error('API Error:', error);
                }
            }

            const needs = (incomeInInr * 0.50).toFixed(2);
            const wants = (incomeInInr * 0.30).toFixed(2);
            const savings = (incomeInInr * 0.20).toFixed(2);

            if (needsEl) needsEl.innerText = '₹ ' + parseFloat(needs).toLocaleString('en-IN');
            if (wantsEl) wantsEl.innerText = '₹ ' + parseFloat(wants).toLocaleString('en-IN');
            if (savingsEl) savingsEl.innerText = '₹ ' + parseFloat(savings).toLocaleString('en-IN');
            overviewIncome.innerText = '₹ ' + incomeInInr.toLocaleString('en-IN', {minimumFractionDigits: 2});

            // Store income globally for savings tracking
            window._budgetIncomeInr = incomeInInr;
            updateActualSavings();
        }

        async function analyzeAndConvert() {
            const input = document.getElementById('smart-input').value.trim();
            const resultDisplay = document.getElementById('conversion-result');
            const rateInfo = document.getElementById('rate-info');
            const pill = document.getElementById('detection-pill');
            const pillLabel = document.getElementById('detected-currency-label');

            if (!input) {
                resultDisplay.innerText = '₹ 0.00';
                pill.style.display = 'none';
                return;
            }

            // Symbols and Codes mapping
            const map = {
                '$': 'usd', 'usd': 'usd',
                '€': 'eur', 'eur': 'eur',
                '£': 'gbp', 'gbp': 'gbp',
                '¥': 'jpy', 'jpy': 'jpy', 'cny': 'jpy',
                'aed': 'aed', 'dh': 'aed',
                '₹': 'inr', 'inr': 'inr', 'rs': 'inr'
            };

            // Enhanced extraction regex: finds numbers (optional decimals) and currency patterns
            const amountMatch = input.match(/(\d+(\.\d+)?)/);
            const textParts = input.toLowerCase().match(/[$€£¥₹]|[a-z]{3}|[a-z]{2}/g) || [];
            
            let amount = amountMatch ? parseFloat(amountMatch[0]) : 0;
            let detectedCurrency = 'usd'; // default

            // Try to find a known currency in the text
            for (let part of textParts) {
                if (map[part]) {
                    detectedCurrency = map[part];
                    break;
                }
            }

            if (amount === 0) {
                resultDisplay.innerText = '₹ 0.00';
                pill.style.display = 'none';
                return;
            }

            // Show detection pill
            pill.style.display = 'block';
            pillLabel.innerText = `Detected: ${detectedCurrency.toUpperCase()} ${amount}`;

            if (detectedCurrency === 'inr') {
                resultDisplay.innerText = '₹ ' + amount.toFixed(2);
                rateInfo.innerText = "Same currency (INR)";
                return;
            }

            try {
                resultDisplay.innerText = "...";
                const response = await fetch(`https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/${detectedCurrency}.json`);
                const data = await response.json();
                const rate = data[detectedCurrency].inr;

                const converted = (amount * rate).toFixed(2);
                resultDisplay.innerText = '₹ ' + converted;
                rateInfo.innerText = `1 ${detectedCurrency.toUpperCase()} = ₹${rate.toFixed(2)}`;
            } catch (error) {
                console.error('Error:', error);
                resultDisplay.innerText = "Error";
            }
        }
        function calculateReturns() {
            const principal = parseFloat(document.getElementById('invest-amount').value) || 0;
            const years = parseInt(document.getElementById('invest-years').value) || 0;

            if (principal === 0 || years === 0) {
                document.getElementById('gold-profit').innerText = '₹ 0.00';
                document.getElementById('sip-profit').innerText = '₹ 0.00';
                return;
            }

            // --- Gold Calculation (Lumpsum) ---
            const goldRate = 0.10; // 10% avg
            const goldFV = principal * Math.pow((1 + goldRate), years);
            const goldProfit = goldFV - principal;

            document.getElementById('gold-profit').innerText = '₹ ' + goldProfit.toLocaleString('en-IN', {maximumFractionDigits: 0});
            document.getElementById('gold-total-val').innerText = 'Projected Value: ₹ ' + goldFV.toLocaleString('en-IN', {maximumFractionDigits: 0});

            // --- SIP Calculation (Monthly) ---
            const annualRate = 15; // 15% avg
            const monthlyRate = (annualRate / 100) / 12;
            const months = years * 12;
            
            // FV = P × ({[1 + i]^n – 1} / i) × (1 + i)
            const sipFV = principal * ( (Math.pow(1 + monthlyRate, months) - 1) / monthlyRate ) * (1 + monthlyRate);
            const totalInvested = principal * months;
            const sipProfit = sipFV - totalInvested;

            document.getElementById('sip-profit').innerText = '₹ ' + sipProfit.toLocaleString('en-IN', {maximumFractionDigits: 0});
            document.getElementById('sip-total-val').innerText = 'Projected Value: ₹ ' + sipFV.toLocaleString('en-IN', {maximumFractionDigits: 0});

            renderInvestmentChart(principal, years);
        }

        let investChartInstance = null;

        function renderInvestmentChart(principal, years) {
            const labels = [];
            const goldData = [];
            const sipData = [];
            const goldRate = 0.10;
            const monthlyRate = (15 / 100) / 12;

            for (let y = 1; y <= years; y++) {
                labels.push('Year ' + y);
                // Gold lumpsum FV
                goldData.push(parseFloat((principal * Math.pow(1 + goldRate, y)).toFixed(0)));
                // SIP cumulative FV
                const m = y * 12;
                const sipFV = principal * ((Math.pow(1 + monthlyRate, m) - 1) / monthlyRate) * (1 + monthlyRate);
                sipData.push(parseFloat(sipFV.toFixed(0)));
            }

            // Badge: best value at final year
            const badge = document.getElementById('invest-final-badge');
            const bestYear = document.getElementById('invest-best-year');
            const bestVal = document.getElementById('invest-best-val');
            const finalSip = sipData[sipData.length - 1] || 0;
            badge.style.display = 'block';
            bestYear.innerText = years;
            bestVal.innerText = '₹ ' + finalSip.toLocaleString('en-IN', {maximumFractionDigits: 0}) + ' (SIP)';

            const ctx = document.getElementById('investChart').getContext('2d');
            if (investChartInstance) investChartInstance.destroy();

            investChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Gold Value (₹)',
                            data: goldData,
                            borderColor: 'gold',
                            backgroundColor: 'rgba(255, 215, 0, 0.1)',
                            borderWidth: 3,
                            pointBackgroundColor: 'gold',
                            pointRadius: 4,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'SIP Value (₹)',
                            data: sipData,
                            borderColor: '#4facfe',
                            backgroundColor: 'rgba(79, 172, 254, 0.1)',
                            borderWidth: 3,
                            pointBackgroundColor: '#4facfe',
                            pointRadius: 4,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    animation: { duration: 1200, easing: 'easeInOutQuart' },
                    plugins: {
                        legend: { labels: { color: '#94a3b8', font: { family: 'Outfit', size: 13 } } },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.dataset.label + ': ₹' + ctx.parsed.y.toLocaleString('en-IN', {maximumFractionDigits: 0})
                            }
                        }
                    },
                    scales: {
                        x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                        y: {
                            ticks: {
                                color: '#94a3b8',
                                callback: v => '₹' + (v >= 1e7 ? (v/1e7).toFixed(1)+'Cr' : v >= 1e5 ? (v/1e5).toFixed(1)+'L' : v.toLocaleString('en-IN'))
                            },
                            grid: { color: 'rgba(255,255,255,0.05)' }
                        }
                    }
                }
            });
        }
        function toggleGoalPlanner() {
            const panel = document.getElementById('goal-planner');
            panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
            if (panel.style.display === 'block') calculateGoal();
        }

        function calculateGoal() {
            const income = window._budgetIncomeInr || 0;
            const price = parseFloat(document.getElementById('goal-price').value) || 0;
            const goalName = document.getElementById('goal-name').value || 'your goal';

            const resultDiv = document.getElementById('goal-result');
            const noIncomeMsg = document.getElementById('goal-no-income');

            if (income === 0) {
                resultDiv.style.display = 'none';
                noIncomeMsg.style.display = 'block';
                return;
            }
            noIncomeMsg.style.display = 'none';

            if (price === 0) { resultDiv.style.display = 'none'; return; }

            // Get actual needs & wants totals already spent
            let needsTotal = 0;
            document.querySelectorAll('#needs-list input[type="number"]').forEach(i => needsTotal += parseFloat(i.value) || 0);
            let wantsTotal = 0;
            document.querySelectorAll('#wants-list input[type="number"]').forEach(i => wantsTotal += parseFloat(i.value) || 0);

            // Available monthly amount = income - needs - wants (actual savings)
            const availableMonthly = income - needsTotal - wantsTotal;

            if (availableMonthly <= 0) {
                document.getElementById('goal-months').innerText = '∞';
                document.getElementById('goal-date').innerText = 'No savings';
                document.getElementById('goal-monthly-alloc').innerText = '₹ 0';
                document.getElementById('goal-status-msg').innerText =
                    '⚠️ You have no savings left after needs & wants. Reduce expenses to save for ' + goalName + '.';
                resultDiv.style.display = 'block';
                return;
            }

            const months = Math.ceil(price / availableMonthly);
            const readyDate = new Date();
            readyDate.setMonth(readyDate.getMonth() + months);
            const readyStr = readyDate.toLocaleString('en-IN', { month: 'long', year: 'numeric' });

            document.getElementById('goal-monthly-alloc').innerText =
                '₹ ' + availableMonthly.toLocaleString('en-IN', { maximumFractionDigits: 0 });
            document.getElementById('goal-months').innerText = months + ' mo';
            document.getElementById('goal-date').innerText = readyStr;
            document.getElementById('goal-status-msg').innerText =
                `✅ Save ₹${availableMonthly.toLocaleString('en-IN',{maximumFractionDigits:0})} every month and you can buy "${goalName}" (₹${price.toLocaleString('en-IN')}) ${months < 2 ? 'next month!' : 'in ' + months + ' months by ' + readyStr + '.'}`;
            resultDiv.style.display = 'block';
        }

        function addRow(type) {
            const list = document.getElementById(type + '-list');
            const div = document.createElement('div');
            div.className = 'expense-row';
            const date = new Date().toISOString().split('T')[0];
            
            div.innerHTML = `
                <input class="row-input" style="flex:1.5;" type="text" placeholder="Name (e.g. Rent)" onchange="updateTransaction(this, '${type}')">
                <input class="row-input" style="flex:1;" type="number" placeholder="Amount" oninput="calculateActualTotal('${type}')" onchange="updateTransaction(this, '${type}')">
                <select class="row-input" style="flex:1;" onchange="updateTransaction(this, '${type}')">
                    <option value="General">General</option>
                    <option value="Food">Food</option>
                    <option value="Travel">Travel</option>
                    <option value="Bills">Bills</option>
                    <option value="Fun">Fun</option>
                </select>
                <input class="row-input date-input" style="flex:1;" type="date" value="${date}" onchange="updateTransaction(this, '${type}')">
                <button class="remove-btn" onclick="removeRow(this, '${type}')"><i class="fas fa-times"></i></button>
            `;
            list.appendChild(div);
        }

        function addPersistedRow(type, data) {
            const list = document.getElementById(type + '-list');
            const div = document.createElement('div');
            div.className = 'expense-row';
            div.dataset.id = data.id;
            
            div.innerHTML = `
                <input class="row-input" style="flex:1.5;" type="text" value="${data.name}" readonly>
                <input class="row-input" style="flex:1;" type="number" value="${data.amount}" readonly>
                <select class="row-input" style="flex:1;" disabled>
                    <option value="${data.category}">${data.category}</option>
                </select>
                <input class="row-input date-input" style="flex:1;" type="date" value="${data.transaction_date}" readonly>
                <button class="remove-btn" onclick="removeRow(this, '${type}')"><i class="fas fa-times"></i></button>
            `;
            list.appendChild(div);
        }

        async function updateTransaction(input, type) {
            const row = input.closest('.expense-row');
            const inputs = row.querySelectorAll('.row-input');
            const name = inputs[0].value;
            const amount = parseFloat(inputs[1].value);
            const category = inputs[2].value;
            const date = inputs[3].value;

            if (name && amount > 0) {
                const res = await saveTransaction(type, { name, amount, category, date });
                if (res.success) {
                    row.dataset.id = res.id;
                    // Make readonly after saving
                    inputs.forEach(i => i.readOnly = true);
                    inputs[2].disabled = true; // select
                    calculateActualTotal(type);
                }
            }
        }

        function removeRow(btn, type) {
            const row = btn.closest('.expense-row');
            if (row.dataset.id) {
                deleteTransaction(row.dataset.id);
            }
            row.remove();
            calculateActualTotal(type);
        }

        function calculateActualTotal(type) {
            const list = document.getElementById(type + '-list');
            const amounts = list.querySelectorAll('input[type="number"]');
            let total = 0;
            amounts.forEach(i => total += parseFloat(i.value) || 0);
            document.getElementById('actual-' + type + '-total').innerText =
                '₹ ' + total.toLocaleString('en-IN', {minimumFractionDigits: 2});
            updateActualSavings();
        }

        function updateActualSavings() {
            let income = window._budgetIncomeInr || 0;
            
            // Fallback to settings if window variable is not set yet
            if (income === 0 && window._currentData && window._currentData.settings) {
                income = parseFloat(window._currentData.settings.monthly_income) || 0;
            }

            let needsTotal = 0;
            document.querySelectorAll('#needs-list input[type="number"]').forEach(i => needsTotal += parseFloat(i.value) || 0);

            let wantsTotal = 0;
            document.querySelectorAll('#wants-list input[type="number"]').forEach(i => wantsTotal += parseFloat(i.value) || 0);

            const savings = income - needsTotal - wantsTotal;
            const savingsEl = document.getElementById('actual-savings');
            const statusEl = document.getElementById('savings-status');
            const card = document.getElementById('actual-savings-card');

            savingsEl.innerText = '₹ ' + savings.toLocaleString('en-IN', {minimumFractionDigits: 2});

            if (income === 0) {
                statusEl.innerText = 'Enter your income & expenses above';
                card.style.borderColor = '#10b981';
                savingsEl.style.color = '#10b981';
            } else if (savings < 0) {
                statusEl.innerText = '⚠️ You are spending more than your income!';
                card.style.borderColor = '#ff4b4b';
                savingsEl.style.color = '#ff4b4b';
            } else if (savings < income * 0.20) {
                statusEl.innerText = '⚠️ Savings below recommended 20% — consider cutting wants.';
                card.style.borderColor = '#f59e0b';
                savingsEl.style.color = '#f59e0b';
            } else {
                statusEl.innerText = '✅ Great! You are saving ' + ((savings / income) * 100).toFixed(1) + '% of your income.';
                card.style.borderColor = '#10b981';
                savingsEl.style.color = '#10b981';
            }

            // Update the charts
            renderSavingsChart(savings);
            renderBudgetPieChart(needsTotal, wantsTotal, savings);
        }

        let savingsChartInstance = null;
        let budgetPieChartInstance = null;

        function renderBudgetPieChart(needs, wants, savings) {
            const ctx = document.getElementById('budgetPieChart').getContext('2d');
            if (budgetPieChartInstance) budgetPieChartInstance.destroy();

            const safeSavings = savings > 0 ? savings : 0;

            budgetPieChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Needs', 'Wants', 'Savings'],
                    datasets: [{
                        data: [needs, wants, safeSavings],
                        backgroundColor: ['#4facfe', '#f59e0b', '#10b981'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    cutout: '70%'
                }
            });

            // Update legend
            const total = needs + wants + safeSavings;
            const getP = (v) => total > 0 ? ((v/total)*100).toFixed(0) : 0;
            document.getElementById('pie-legend').innerHTML = `
                <div style="display:flex; gap:15px;">
                    <span><i class="fas fa-circle" style="color:#4facfe;"></i> Needs: ${getP(needs)}%</span>
                    <span><i class="fas fa-circle" style="color:#f59e0b;"></i> Wants: ${getP(wants)}%</span>
                    <span><i class="fas fa-circle" style="color:#10b981;"></i> Savings: ${getP(safeSavings)}%</span>
                </div>
            `;
        }

        function renderSavingsChart(monthlySavings) {
            const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            const cumulative = [];
            const monthly = [];
            for (let i = 0; i < 12; i++) {
                monthly.push(monthlySavings > 0 ? monthlySavings : 0);
                cumulative.push((i + 1) * (monthlySavings > 0 ? monthlySavings : 0));
            }

            const annualTotal = monthlySavings > 0 ? monthlySavings * 12 : 0;
            const badge = document.getElementById('annual-total-badge');
            const annualVal = document.getElementById('annual-total-value');
            if (annualTotal > 0) {
                badge.style.display = 'block';
                annualVal.innerText = '₹ ' + annualTotal.toLocaleString('en-IN', {maximumFractionDigits: 0});
            } else {
                badge.style.display = 'none';
            }

            const ctx = document.getElementById('savingsChart').getContext('2d');
            if (savingsChartInstance) savingsChartInstance.destroy();

            savingsChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Monthly Savings (₹)',
                            data: monthly,
                            backgroundColor: 'rgba(79, 172, 254, 0.3)',
                            borderColor: '#4facfe',
                            borderWidth: 2,
                            borderRadius: 8,
                            yAxisID: 'y'
                        },
                        {
                            type: 'line',
                            label: 'Cumulative Savings (₹)',
                            data: cumulative,
                            borderColor: '#00f2fe',
                            backgroundColor: 'rgba(0, 242, 254, 0.1)',
                            borderWidth: 3,
                            pointBackgroundColor: '#00f2fe',
                            pointRadius: 5,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    animation: { duration: 1000, easing: 'easeInOutQuart' },
                    plugins: {
                        legend: { labels: { color: '#94a3b8', font: { family: 'Outfit' } } },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.dataset.label + ': ₹' + ctx.parsed.y.toLocaleString('en-IN', {maximumFractionDigits: 0})
                            }
                        }
                    },
                    scales: {
                        x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                        y: {
                            type: 'linear', position: 'left',
                            ticks: { color: '#4facfe', callback: v => '₹' + v.toLocaleString('en-IN') },
                            grid: { color: 'rgba(255,255,255,0.05)' }
                        },
                        y1: {
                            type: 'linear', position: 'right',
                            ticks: { color: '#00f2fe', callback: v => '₹' + v.toLocaleString('en-IN') },
                            grid: { drawOnChartArea: false }
                        }
                    }
                }
            });
        }

        function prefillBudgetSymbol(symbol) {
            const input = document.getElementById('budget-income-input');
            const currentVal = input.value.match(/(\d+(\.\d+)?)/);
            const amount = currentVal ? currentVal[0] : '';
            input.value = symbol + amount;
            analyzeAndBudget();
            input.focus();
        }

        function prefillSymbol(symbol) {
            const input = document.getElementById('smart-input');
            const currentVal = input.value.match(/(\d+(\.\d+)?)/);
            const amount = currentVal ? currentVal[0] : '';
            input.value = symbol + amount;
            analyzeAndConvert();
            input.focus();
        }
    </script>
</body>
</html>
