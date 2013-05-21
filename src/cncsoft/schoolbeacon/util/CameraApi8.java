package cncsoft.schoolbeacon.util;

import android.hardware.Camera;

public class CameraApi8 {
	public static void setCameraOrientation(Camera camera, int orientation) {
		camera.setDisplayOrientation(orientation);
	}
}
