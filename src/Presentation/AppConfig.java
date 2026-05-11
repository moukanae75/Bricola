    package Presentation;

    import java.sql.Connection;

import application.usecases.*;
    import domain.repositories.IArtisanReader;
    import domain.repositories.IArtisanWriter;
import infrastructure.Config.DatabaseConnection;
import infrastructure.persistence.*;

    public class AppConfig {
        //hada mini IOC di container hittach kanwiri fih manuelement 
        public static MainFrame createMainFrame() {

            Connection conn = DatabaseConnection.getConnection();
            MySQLUserRepo userRepo = new MySQLUserRepo(conn);


            // hadii dependency injection 
            MySQLArtisanRepo artisanRepoR = new MySQLArtisanRepo(conn);
            MySQLArtisanRepo artisanRepoW = new MySQLArtisanRepo(conn);
            MySQLCategory categoryRepoR = new MySQLCategory(conn);
            MySQLCategory categoryRepoW = new MySQLCategory(conn);
            MySQLEvaluationRepo evaluationRepo = new MySQLEvaluationRepo(conn);
            MySQLServiceRequestRepo serviceRequestRepo = new MySQLServiceRequestRepo(conn);
            IArtisanReader reader = artisanRepoR;
            IArtisanWriter writer = artisanRepoW;


            return new MainFrame( // prizat manuelle (wiring)
                new VerifyArtisan(reader, writer),  
                new ApproveCategory(categoryRepoW, categoryRepoR),
                new RejectCategory(categoryRepoW, categoryRepoR),
                new ComputeArtisanAvgRating(artisanRepoR, artisanRepoW, evaluationRepo),
                new SmartArtisanMatcher(artisanRepoR, evaluationRepo),
                new DynamicPricingSuggestion(serviceRequestRepo),
                new ChurnRiskPredictor(userRepo, serviceRequestRepo),
                new WorkloadBalancer(artisanRepoR, serviceRequestRepo),
                new GetServiceStatus(serviceRequestRepo),
                new AttachMediaToRequest(serviceRequestRepo),
                artisanRepoR,
                categoryRepoR,
                evaluationRepo,
                serviceRequestRepo
            );
        }
    }