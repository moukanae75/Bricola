package Presentation;

import application.usecases.*;
import domain.entities.*;
import domain.repositories.IArtisanReader;
import domain.repositories.ICategoryReader;
import domain.repositories.IEvaluation;
import domain.repositories.IServiceRequest;


import javax.swing.*;
import javax.swing.border.EmptyBorder;
import java.awt.*;
import java.awt.geom.RoundRectangle2D;
import java.util.List;

public class MainFrame extends JFrame {

    // ── Palette ───────────────────────────────────────────────────────────
    private final Color COLOR_BG      = new Color(248, 249, 250);
    private final Color COLOR_SIDEBAR  = new Color(33, 37, 41);
    private final Font  FONT_TITLE    = new Font("Inter", Font.BOLD, 22);
    private final Font  FONT_LABEL    = new Font("Inter", Font.PLAIN, 14);

    // ── Use-cases ─────────────────────────────────────────────────────────
    private final VerifyArtisan           verifyArtisan;
    private final ApproveCategory         approveCategory;
    private final RejectCategory          rejectCategory;
    private final ComputeArtisanAvgRating computeAvgRating;
    private final SmartArtisanMatcher     matcher;
    private final DynamicPricingSuggestion pricing;
    private final ChurnRiskPredictor      churnPredictor;
    private final WorkloadBalancer        workloadBalancer;
    private final GetServiceStatus        getStatus;
    private final AttachMediaToRequest    attachMedia;

        // ── Repos (for direct queries in screens) ────────────────────────────
        private final IArtisanReader       artisanRepoR;
        private final ICategoryReader         categoryRepo;
        private final IEvaluation     evalRepo;
        private final IServiceRequest srRepo;

    // ── Content panel (swapped when navigating) ───────────────────────────
    private JPanel contentArea;

    public MainFrame(
            VerifyArtisan           verifyArtisan,
            ApproveCategory         approveCategory,
            RejectCategory          rejectCategory,
            ComputeArtisanAvgRating computeAvgRating,
            SmartArtisanMatcher     matcher,
            DynamicPricingSuggestion pricing,
            ChurnRiskPredictor      churnPredictor,
            WorkloadBalancer        workloadBalancer,
            GetServiceStatus        getStatus,
            AttachMediaToRequest    attachMedia,
            IArtisanReader        artisanRepoR,
            ICategoryReader           categoryRepo,
            IEvaluation     evalRepo,
            IServiceRequest srRepo) {

        this.verifyArtisan    = verifyArtisan;
        this.approveCategory  = approveCategory;
        this.rejectCategory   = rejectCategory;
        this.computeAvgRating = computeAvgRating;
        this.matcher          = matcher;
        this.pricing          = pricing;
        this.churnPredictor   = churnPredictor;
        this.workloadBalancer = workloadBalancer;
        this.getStatus        = getStatus;
        this.attachMedia      = attachMedia;
        this.artisanRepoR      = artisanRepoR;
        this.categoryRepo     = categoryRepo;
        this.evalRepo         = evalRepo;
        this.srRepo           = srRepo;

        setTitle("BRICOLA - Artisan Management");
        setSize(1100, 720);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        getContentPane().setBackground(COLOR_BG);

        initUI();
    }

    // ── UI construction ───────────────────────────────────────────────────

    private void initUI() {
        setLayout(new BorderLayout());

        // Sidebar
        JPanel sidebar = new JPanel();
        sidebar.setPreferredSize(new Dimension(240, 0));
        sidebar.setBackground(COLOR_SIDEBAR);
        sidebar.setLayout(new BoxLayout(sidebar, BoxLayout.Y_AXIS));
        sidebar.setBorder(new EmptyBorder(30, 20, 30, 20));

        JLabel brand = new JLabel("BRICOLA");
        brand.setForeground(Color.WHITE);
        brand.setFont(FONT_TITLE);
        brand.setAlignmentX(Component.LEFT_ALIGNMENT);
        sidebar.add(brand);
        sidebar.add(Box.createRigidArea(new Dimension(0, 40)));

        sidebar.add(createMenuButton("🏠  Dashboard",       e -> showDashboard()));
        sidebar.add(Box.createRigidArea(new Dimension(0, 8)));
        sidebar.add(createMenuButton("✅  Verify Artisan",   e -> openVerifyArtisan()));
        sidebar.add(Box.createRigidArea(new Dimension(0, 8)));
        sidebar.add(createMenuButton("📂  Categories",       e -> openCategories()));
        sidebar.add(Box.createRigidArea(new Dimension(0, 8)));
        sidebar.add(createMenuButton("📊  Churn Predictor",  e -> openChurnScreen()));
        sidebar.add(Box.createRigidArea(new Dimension(0, 8)));
        sidebar.add(createMenuButton("💰  Pricing Strategy", e -> openPricingScreen()));
        
        add(sidebar, BorderLayout.WEST);

        // Main content area
        contentArea = new JPanel(new BorderLayout());
        contentArea.setOpaque(false);
        add(contentArea, BorderLayout.CENTER);

        showDashboard();
    }

    // ── Screens ───────────────────────────────────────────────────────────

   

    private void showDashboard() {
        contentArea.removeAll();

        JPanel panel = new JPanel(new BorderLayout());
        panel.setOpaque(false);
        panel.setBorder(new EmptyBorder(40, 40, 40, 40));

        JLabel header = new JLabel("Dashboard");
        header.setFont(FONT_TITLE);
        panel.add(header, BorderLayout.NORTH);

        // Pull live counts from DB
        int artisanCount  = artisanRepoR.findAll().size();
        int pendingCats   = categoryRepo.findPending().size();
        int pendingReqs   = srRepo.findPending().size();

        JPanel cards = new JPanel(new GridLayout(1, 3, 20, 0));
        cards.setOpaque(false);
        cards.setBorder(new EmptyBorder(30, 0, 0, 0));
        cards.add(createStatCard("Total Artisans",      String.valueOf(artisanCount)));
        cards.add(createStatCard("Pending Categories",  String.valueOf(pendingCats)));
        cards.add(createStatCard("Pending Requests",    String.valueOf(pendingReqs)));

        panel.add(cards, BorderLayout.CENTER);

        contentArea.add(panel);
        contentArea.revalidate();
        contentArea.repaint();
    }

    private void openVerifyArtisan() {
        contentArea.removeAll();

        JPanel panel = new JPanel(new BorderLayout());
        panel.setOpaque(false);
        panel.setBorder(new EmptyBorder(40, 40, 40, 40));

        JLabel header = new JLabel("Verify Artisan");
        header.setFont(FONT_TITLE);
        panel.add(header, BorderLayout.NORTH);

        // Load unverified artisans
        List<Artisan> artisans = artisanRepoR.findAll();

        String[] cols = {"ID", "Name", "City", "Verified"};
        Object[][] data = artisans.stream()
            .map(a -> new Object[]{a.getId(), a.getName(), a.getCity(), a.isVerified()})
            .toArray(Object[][]::new);

        JTable table = new JTable(data, cols);
        table.setRowHeight(28);
        JScrollPane scroll = new JScrollPane(table);

        JButton verifyBtn = new JButton("Verify Selected");
        verifyBtn.addActionListener(e -> {
            int row = table.getSelectedRow();
            if (row == -1) { JOptionPane.showMessageDialog(this, "Select an artisan first."); return; }
            int id = (int) table.getValueAt(row, 0);
            try {
                boolean ok = verifyArtisan.execute(id);
                JOptionPane.showMessageDialog(this, ok ? "Artisan verified!" : "Could not verify.");
                openVerifyArtisan(); // refresh
            } catch (IllegalStateException ex) {
                JOptionPane.showMessageDialog(this, ex.getMessage(), "Warning", JOptionPane.WARNING_MESSAGE);
            }
        });

        panel.add(scroll, BorderLayout.CENTER);
        panel.add(verifyBtn, BorderLayout.SOUTH);

        contentArea.add(panel);
        contentArea.revalidate();
        contentArea.repaint();
    }

    private void openCategories() {
        contentArea.removeAll();

        JPanel panel = new JPanel(new BorderLayout());
        panel.setOpaque(false);
        panel.setBorder(new EmptyBorder(40, 40, 40, 40));

        JLabel header = new JLabel("Pending Categories");
        header.setFont(FONT_TITLE);
        panel.add(header, BorderLayout.NORTH);

        List<Category> pending = categoryRepo.findPending();

        String[] cols = {"ID", "Name", "Approved", "Pending"};
        Object[][] data = pending.stream()
            .map(c -> new Object[]{c.getId(), c.getName(), c.getIsApproved(), c.getIsPending()})
            .toArray(Object[][]::new);

        JTable table = new JTable(data, cols);
        table.setRowHeight(28);

        JButton approveBtn = new JButton("Approve");
        JButton rejectBtn  = new JButton("Reject");

        approveBtn.addActionListener(e -> {
            int row = table.getSelectedRow();
            if (row == -1) { JOptionPane.showMessageDialog(this, "Select a category first."); return; }
            int id = (int) table.getValueAt(row, 0);
            approveCategory.execute(id);
            JOptionPane.showMessageDialog(this, "Category approved.");
            openCategories();
        });

        rejectBtn.addActionListener(e -> {
            int row = table.getSelectedRow();
            if (row == -1) { JOptionPane.showMessageDialog(this, "Select a category first."); return; }
            int id = (int) table.getValueAt(row, 0);
            rejectCategory.execute(id);
            JOptionPane.showMessageDialog(this, "Category rejected.");
            openCategories();
        });

        JPanel buttons = new JPanel(new FlowLayout(FlowLayout.LEFT));
        buttons.setOpaque(false);
        buttons.add(approveBtn);
        buttons.add(rejectBtn);

        panel.add(new JScrollPane(table), BorderLayout.CENTER);
        panel.add(buttons, BorderLayout.SOUTH);

        contentArea.add(panel);
        contentArea.revalidate();
        contentArea.repaint();
    }

    private void openChurnScreen() {
        contentArea.removeAll();

        JPanel panel = new JPanel(new BorderLayout());
        panel.setOpaque(false);
        panel.setBorder(new EmptyBorder(40, 40, 40, 40));

        JLabel header = new JLabel("Churn Risk Predictor");
        header.setFont(FONT_TITLE);
        panel.add(header, BorderLayout.NORTH);

        JLabel info = new JLabel("<html><i>Shows users inactive for more than 30 days.</i></html>");
        info.setBorder(new EmptyBorder(10, 0, 20, 0));

        JButton runBtn = new JButton("Run Prediction");
        JTextArea output = new JTextArea(15, 60);
        output.setEditable(false);

        runBtn.addActionListener(e -> {
            List<ChurnRiskPredictor.ChurnRisk> risks = churnPredictor.execute(30);
            if (risks.isEmpty()) {
                output.setText("No churn risks detected.");
            } else {
                StringBuilder sb = new StringBuilder();
                risks.forEach(r -> sb.append(r).append("\n"));
                output.setText(sb.toString());
            }
        });

        JPanel top = new JPanel(new BorderLayout());
        top.setOpaque(false);
        top.add(info, BorderLayout.NORTH);
        top.add(runBtn, BorderLayout.SOUTH);

        panel.add(top, BorderLayout.NORTH);
        panel.add(new JScrollPane(output), BorderLayout.CENTER);

        contentArea.add(panel);
        contentArea.revalidate();
        contentArea.repaint();
    }

    private void openPricingScreen() {
        contentArea.removeAll();

        JPanel panel = new JPanel(new BorderLayout());
        panel.setOpaque(false);
        panel.setBorder(new EmptyBorder(40, 40, 40, 40));

        JLabel header = new JLabel("Dynamic Pricing Strategy");
        header.setFont(FONT_TITLE);
        panel.add(header, BorderLayout.NORTH);

        JPanel form = new JPanel(new GridLayout(4, 2, 10, 10));
        form.setOpaque(false);
        form.setBorder(new EmptyBorder(20, 0, 20, 200));

        form.add(new JLabel("Artisan ID:"));   JTextField artisanIdFld  = new JTextField(); form.add(artisanIdFld);
        form.add(new JLabel("Category ID:"));  JTextField categoryIdFld = new JTextField(); form.add(categoryIdFld);
        form.add(new JLabel("Your Price (MAD):")); JTextField priceFld  = new JTextField(); form.add(priceFld);
        JButton calcBtn = new JButton("Get Advice"); form.add(new JLabel()); form.add(calcBtn);

        JTextArea result = new JTextArea(6, 50);
        result.setEditable(false);

        calcBtn.addActionListener(e -> {
            try {
                int    aid   = Integer.parseInt(artisanIdFld.getText().trim());
                int    cid   = Integer.parseInt(categoryIdFld.getText().trim());
                double price = Double.parseDouble(priceFld.getText().trim());

                Artisan artisan = artisanRepoR.findById(aid);
                if (artisan == null) { result.setText("Artisan not found."); return; }

                DynamicPricingSuggestion.PricingAdvice advice = pricing.execute(artisan, cid, price);
                result.setText(advice + "\n\n" + advice.explanation);
            } catch (NumberFormatException ex) {
                result.setText("Please enter valid numbers.");
            }
        });

        panel.add(form, BorderLayout.NORTH);
        panel.add(new JScrollPane(result), BorderLayout.CENTER);

        contentArea.add(panel);
        contentArea.revalidate();
        contentArea.repaint();
    }

    // ── Reusable component factories ──────────────────────────────────────

    private JButton createMenuButton(String text, java.awt.event.ActionListener action) {
        JButton btn = new JButton(text);
        btn.setMaximumSize(new Dimension(Integer.MAX_VALUE, 45));
        btn.setFont(FONT_LABEL);
        btn.setForeground(new Color(200, 200, 200));
        btn.setBackground(COLOR_SIDEBAR);
        btn.setFocusPainted(false);
        btn.setBorderPainted(false);
        btn.setContentAreaFilled(false);
        btn.setHorizontalAlignment(SwingConstants.LEFT);
        btn.setCursor(new Cursor(Cursor.HAND_CURSOR));
        btn.addMouseListener(new java.awt.event.MouseAdapter() {
            public void mouseEntered(java.awt.event.MouseEvent e) { btn.setForeground(Color.WHITE); }
            public void mouseExited(java.awt.event.MouseEvent e)  { btn.setForeground(new Color(200, 200, 200)); }
        });
        btn.addActionListener(action);
        return btn;
    }

    private JPanel createStatCard(String title, String value) {
        JPanel card = new JPanel() {
            @Override protected void paintComponent(Graphics g) {
                Graphics2D g2 = (Graphics2D) g.create();
                g2.setRenderingHint(RenderingHints.KEY_ANTIALIASING, RenderingHints.VALUE_ANTIALIAS_ON);
                g2.setColor(Color.WHITE);
                g2.fill(new RoundRectangle2D.Double(0, 0, getWidth(), getHeight(), 15, 15));
                g2.dispose();
            }
        };
        card.setLayout(new GridLayout(2, 1));
        card.setBorder(new EmptyBorder(20, 20, 20, 20));
        card.setOpaque(false);

        JLabel t = new JLabel(title);  t.setForeground(Color.GRAY);
        JLabel v = new JLabel(value);  v.setFont(new Font("Inter", Font.BOLD, 28));
        card.add(t); card.add(v);
        return card;
    }
}