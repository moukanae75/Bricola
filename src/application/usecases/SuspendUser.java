package application.usecases;

import domain.entities.User;
import domain.repositories.IUser;

public class SuspendUser {

    private final IUser userRepository;

    public SuspendUser(IUser userRepository) {
        this.userRepository = userRepository;
    }

    public void execute(Integer userId) {
        User user = userRepository.findById(userId);
        if (user != null) {
            user.suspend();
            userRepository.update(user);
            System.out.println("User #" + userId + " has been suspended.");
        }
    }
}
