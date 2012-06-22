package cnc.sosbeacon.util;

public class TextUtil {
	public static String test = "test";
	
	public static String removeHTML(String htmlString) {
         // Remove HTML tag from java String    
        String noHTMLString = htmlString.replaceAll("\\<.*?\\>", "");
        // Remove Carriage return from java String
        noHTMLString = noHTMLString.replaceAll("\r", "<br/>");
        // Remove New line from java string and replace html break
        noHTMLString = noHTMLString.replaceAll("\n", " ");
        noHTMLString = noHTMLString.replaceAll("\'", "&#39;");
        noHTMLString = noHTMLString.replaceAll("\"", "&quot;");
        return noHTMLString.trim();
    }
	
	public static String removeSpecialCharacters(String str) {
		StringBuilder sb = new StringBuilder();
		for (int i = 0; i < str.length(); i++) {
			char ch = str.charAt(i);
			if (ch >= '0' && ch <= '9') {
				sb.append(ch);
			}
		}
		// using java.util.regex.regex.Replace(str, "[^a-zA-Z0-9_.]+", "", RegexOptions.Compiled);
		return sb.toString();
	}
	
	public static boolean allowPhoneCharacters(String str) {
		for (int i = 0; i < str.length(); i++) {
			char ch = str.charAt(i);
			if ((ch < '0' || ch > '9') && ch != '(' && ch != ')' && ch != ' ' && ch != '.' && ch != '-') {
				return false;
			}
		}
		return true;
	}
	
	public static String removePhoneCharacters(String str) {
		if (str.startsWith("1") || str.startsWith("+")) {
			str = str.substring(1);
		}
		if (str.startsWith("1") || str.startsWith("+")) {
			str = str.substring(1);
		}
		return str;
	}
}
