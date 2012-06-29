
package cnc.schoolbeacon.model;

public class User {
    public String userId;

    public String name;

    public String email;

    public String textphone;

    public String voicephone;

    public User(String userId, String name, String email, String textphone) {
        this.userId = userId;
        this.name = name;
        this.textphone = textphone;
        this.email = email;
    }
}
