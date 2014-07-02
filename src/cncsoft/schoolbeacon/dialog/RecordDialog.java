package cncsoft.schoolbeacon.dialog;
import android.app.Dialog;
import android.content.Context;
import android.os.Bundle;
import cncsoft.schoolbeacon.R;

public class RecordDialog extends Dialog {

	public RecordDialog(Context context) {
		super(context);
	}
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.record_dialog);
	}
}
