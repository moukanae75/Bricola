package Presentation;

import javax.swing.*;
import javax.swing.border.EmptyBorder;
import java.awt.*;
import java.awt.geom.RoundRectangle2D;

public class MainFrame extends JFrame {

    // Editorial Palette
    private final Color COLOR_BG = new Color(248, 249, 250);     // Off-white
    private final Color COLOR_SIDEBAR = new Color(33, 37, 41);   // Deep Charcoal
    private final Font FONT_TITLE = new Font("Inter", Font.BOLD, 22);
    private final Font FONT_LABEL = new Font("Inter", Font.PLAIN, 14);

    public MainFrame() {
        setTitle("BRICOLA - Artisan Management");
        setSize(1000, 700);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        getContentPane().setBackground(COLOR_BG);

        initUI();
    }

    private void initUI() {
        setLayout(new BorderLayout());

        // --- Sidebar (Navigation) ---
        JPanel sidebar = new JPanel();
        sidebar.setPreferredSize(new Dimension(250, 0));
        sidebar.setBackground(COLOR_SIDEBAR);
        sidebar.setLayout(new BoxLayout(sidebar, BoxLayout.Y_AXIS));
        sidebar.setBorder(new EmptyBorder(30, 20, 30, 20));

        JLabel brand = new JLabel("BRICOLA");
        brand.setForeground(Color.WHITE);
        brand.setFont(FONT_TITLE);
        brand.setAlignmentX(Component.LEFT_ALIGNMENT);
        
        sidebar.add(brand);
        sidebar.add(Box.createRigidArea(new Dimension(0, 40)));

        // Stylish Menu Buttons
        sidebar.add(createMenuButton("Verify Artisan", e -> openVerifyArtisan()));
        sidebar.add(Box.createRigidArea(new Dimension(0, 10)));
        sidebar.add(createMenuButton("Churn Predictor", e -> openChurnScreen()));
        sidebar.add(Box.createRigidArea(new Dimension(0, 10)));
        sidebar.add(createMenuButton("Pricing Strategy", e -> System.out.println("Pricing")));

        add(sidebar, BorderLayout.WEST);

        // --- Content Area (Dashboard View) ---
        JPanel content = new JPanel(new BorderLayout());
        content.setOpaque(false);
        content.setBorder(new EmptyBorder(40, 40, 40, 40));

        JLabel header = new JLabel("Welcome back, Mohammed");
        header.setFont(FONT_TITLE);
        content.add(header, BorderLayout.NORTH);

        // Placeholder for the "Cards" view
        JPanel cardContainer = new JPanel(new GridLayout(1, 2, 20, 0));
        cardContainer.setOpaque(false);
        cardContainer.add(createStatCard("Active Artisans", "1,284"));
        cardContainer.add(createStatCard("Avg. Churn Risk", "12.4%"));
        
        content.add(cardContainer, BorderLayout.CENTER);

        add(content, BorderLayout.CENTER);
    }

    // --- Modern Component Factories ---

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
            public void mouseExited(java.awt.event.MouseEvent e) { btn.setForeground(new Color(200, 200, 200)); }
        });

        btn.addActionListener(action);
        return btn;
    }

    private JPanel createStatCard(String title, String value) {
        JPanel card = new JPanel() {
            @Override
            protected void paintComponent(Graphics g) {
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

        JLabel t = new JLabel(title);
        t.setForeground(Color.GRAY);
        JLabel v = new JLabel(value);
        v.setFont(new Font("Inter", Font.BOLD, 28));

        card.add(t);
        card.add(v);
        return card;
    }

    private void openVerifyArtisan() { /* implementation */ }
    private void openChurnScreen() { /* implementation */ }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> new MainFrame().setVisible(true));
    }
}