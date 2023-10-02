import java.util.regex.*;
import java.io.*;
import java.sql.*;
import java.util.*;


public class parseResults {
    static  parseResultsRun myRun;
    public static void main(String[] args) throws Exception {
	myRun = new parseResultsRun();
	myRun.run(args);
    }
}
