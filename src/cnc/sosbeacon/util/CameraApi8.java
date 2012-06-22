package cnc.sosbeacon.util;

import android.hardware.Camera;

public class CameraApi8 {
	public static void setCameraOrientation(Camera camera, int orientation) {
		camera.setDisplayOrientation(orientation);
	}
}
