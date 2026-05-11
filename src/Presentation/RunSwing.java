package Presentation;

import javax.swing.SwingUtilities;

public class RunSwing {
    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> {
            AppConfig.createMainFrame().setVisible(true);
        });    
    }
}
