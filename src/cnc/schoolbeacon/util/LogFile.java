package cnc.schoolbeacon.util;

import cnc.schoolbeacon.constants.Constants;

import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

public class LogFile implements Constants {

	public static void log(Level level, Object message) {
		Logger logger = LoggerFactory.getLogger();
		try {
			logger.log(level, message);
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
}
