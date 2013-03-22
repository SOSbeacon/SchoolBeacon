
package cnc.schoolbeacon.provider;

import android.content.ContentProvider;
import android.content.ContentUris;
import android.content.ContentValues;
import android.content.Context;
import android.content.UriMatcher;
import android.database.Cursor;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;
import android.database.sqlite.SQLiteQueryBuilder;
import android.net.Uri;
import cnc.schoolbeacon.constants.Constants;

import com.google.code.microlog4android.Level;
import com.google.code.microlog4android.Logger;
import com.google.code.microlog4android.LoggerFactory;

public class SosBeaconCheckinMessageProvider extends ContentProvider {

    protected final Logger logger = LoggerFactory.getLogger(SosBeaconCheckinMessageProvider.class);

    public static final UriMatcher uriMatcher;

    public static final Uri CONTENT_URI = Uri.parse("content://" + Constants.PROVIDER_NAME
            + "/checkin");
    static {
        uriMatcher = new UriMatcher(UriMatcher.NO_MATCH);
        uriMatcher.addURI(Constants.PROVIDER_NAME, "checkin", 1);
        uriMatcher.addURI(Constants.PROVIDER_NAME, "checkin/#", 2);
    }

    //Using SQLiteDatabase to store all content provider data

    private SQLiteDatabase db;

    private static final String DATABASE_NAME = Constants.DATABASE;

    private static final int DATABASE_VERSION = 1;

    private static class DatabaseHelper extends SQLiteOpenHelper {

        DatabaseHelper(Context context) {
            super(context, DATABASE_NAME, null, DATABASE_VERSION);
        }

        @Override
        public void onCreate(SQLiteDatabase db) {
            db.execSQL(Constants.DATABASE_CREATE);
        }

        @Override
        public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
            db.execSQL("DROP TABLE IF EXISTS " + Constants.DATABASE);
            onCreate(db);

        }
    }

    @Override
    public int delete(Uri uri, String selection, String[] selectionArgs) {
        db.delete(Constants.DATABASE, selection, selectionArgs);
        return 0;
    }

    @Override
    public String getType(Uri uri) {
        return null;
    }

    @Override
    public Uri insert(Uri uri, ContentValues values) {
        long rowID = db.insert(Constants.DATABASE, "", values);
        if (rowID > 0) {
            Uri mUri = ContentUris.withAppendedId(CONTENT_URI, rowID);
            getContext().getContentResolver().notifyChange(mUri, null);
            return mUri;

        }
        throw new SQLException("Failed to insert new row into " + uri);
    }

    @Override
    public boolean onCreate() {
        try {
            Context context = getContext();
            DatabaseHelper dbHelper = new DatabaseHelper(context);
            db = dbHelper.getReadableDatabase();
        } catch (Exception e) {
            logger.log(Level.FATAL, "onCreate(): " + e.getMessage());
        }
        return (db == null) ? false : true;
    }

    @Override
    public Cursor query(Uri uri, String[] projection, String selection, String[] selectionArgs,
            String sortOrder) {
        SQLiteQueryBuilder sqlBuilder = new SQLiteQueryBuilder();
        sqlBuilder.setTables(Constants.DATABASE);
        try {
            Cursor c = sqlBuilder.query(db, projection, selection, selectionArgs, null, null,
                    sortOrder);
            c.setNotificationUri(getContext().getContentResolver(), uri);
            return c;
        } catch (Exception e) {
            logger.log(Level.FATAL, "query(): " + e.getMessage());
        }
        return null;

    }

    @Override
    public int update(Uri uri, ContentValues values, String where, String[] whereArgs) {
        int count = 0;
        try {
            count = db.update(Constants.DATABASE, values, where, whereArgs);
            getContext().getContentResolver().notifyChange(uri, null);
        } catch (Exception e) {
            logger.log(Level.FATAL, "update(): " + e.getMessage());
        }
        return count;
    }
}
